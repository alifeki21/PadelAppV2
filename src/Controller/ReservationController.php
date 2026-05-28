<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\CourtRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    // ── 1. Affiche la page HTML ──────────────────────────────────────────
    #[Route('/reservation', name: 'app_reservation', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(CourtRepository $courtRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'courts' => $courtRepository->findAll(),
        ]);
    }

    // ── 2. GET : créneaux déjà pris pour un terrain + date ───────────────
    #[Route('/reservation/slots', name: 'app_reservation_slots', methods: ['GET'])]
    public function getBookedSlots(
        Request $request,
        CourtRepository $courtRepository,
        ReservationRepository $reservationRepository
    ): JsonResponse {

        $court = $courtRepository->find((int) $request->query->get('court_number'));
        if (!$court) {
            return $this->json(['success' => false, 'message' => 'Terrain introuvable.'], 400);
        }

        $dateStr = $request->query->get('reservation_date', '');
        if (!$this->isValidDate($dateStr)) {
            return $this->json(['success' => false, 'message' => 'Date invalide.'], 400);
        }

        $reservations = $reservationRepository->findBy([
            'court'           => $court,
            'reservationDate' => new \DateTime($dateStr),
        ]);

        $bookedSlots = array_map(
            fn(Reservation $r) => $r->getReservationTime()->format('H:i'),
            $reservations
        );

        return $this->json(['success' => true, 'bookedSlots' => $bookedSlots]);
    }

    // ── 3. POST : enregistre la réservation (appelé par le JS) ──────────
    #[Route('/reservation/save', name: 'app_reservation_save', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function save(
        Request $request,
        CourtRepository $courtRepository,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $court = $courtRepository->find((int) $request->request->get('court_number'));
        if (!$court) {
            return $this->json(['success' => false, 'message' => 'Veuillez choisir un terrain valide.']);
        }

        $dateStr     = $request->request->get('reservation_date', '');
        $timeStr     = $request->request->get('reservation_time', '');
        $playerCount = (int) $request->request->get('player_count', 4);
        $requirements = trim($request->request->get('requirements', ''));

        if (!$this->isValidDate($dateStr)) {
            return $this->json(['success' => false, 'message' => 'Veuillez choisir une date valide.']);
        }

        if (!$this->isValidTime($timeStr)) {
            return $this->json(['success' => false, 'message' => 'Veuillez choisir un créneau valide.']);
        }

        if (!in_array($playerCount, [2, 3, 4], true)) {
            return $this->json(['success' => false, 'message' => 'Nombre de joueurs invalide.']);
        }

        if (strlen($requirements) > 500) {
            return $this->json(['success' => false, 'message' => 'Demandes trop longues (max 500 caractères).']);
        }

        $date = new \DateTime($dateStr);
        $time = new \DateTime($timeStr);

        // Créneau déjà pris ?
        $existing = $reservationRepository->findOneBy([
            'court'           => $court,
            'reservationDate' => $date,
            'reservationTime' => $time,
        ]);

        if ($existing) {
            return $this->json([
                'success' => false,
                'message' => 'Ce créneau est déjà réservé. Veuillez en choisir un autre.',
            ]);
        }

        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setCourt($court);
        $reservation->setReservationDate($date);
        $reservation->setReservationTime($time);
        $reservation->setPlayerCount($playerCount);
        $reservation->setRequirements($requirements ?: null);
        $reservation->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Réservation confirmée.',
            'reservation' => [
                'courtNumber' => $court->getId(),
                'date'        => $dateStr,
                'time'        => substr($timeStr, 0, 5),
                'playerCount' => $playerCount,
            ],
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────
    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date && $date >= date('Y-m-d');
    }

    private function isValidTime(string $time): bool
    {
        return (bool) preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time);
    }
}