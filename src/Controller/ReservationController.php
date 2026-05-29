<?php

namespace App\Controller;

use App\Entity\Court;
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
    public function index(CourtRepository $courtRepository, EntityManagerInterface $entityManager): Response
    {
        $courts = $courtRepository->findAll();

        if (empty($courts) || isset($_GET['force_refresh'])) {
            foreach ($courts as $oldCourt) {
                $entityManager->remove($oldCourt);
            }
            $entityManager->flush();

            $court1 = new Court();
            $court1->setName('Court 1 - Cupra');
            $court1->setIsIndoor(0);
            $court1->setPricePerHour(25.00); 
            $court1->setImage('cupra.png');
            $entityManager->persist($court1);

            $court2 = new Court();
            $court2->setName('Court 2 - Decathlon');
            $court2->setIsIndoor(1);
            $court2->setPricePerHour(25.00);
            $court2->setImage('dechatlon.png');
            $entityManager->persist($court2);

            $court3 = new Court();
            $court3->setName('Court 3 - Codeforces');
            $court3->setIsIndoor(0);
            $court3->setPricePerHour(25.00);
            $court3->setImage('codeforces.png');
            $entityManager->persist($court3);

            $entityManager->flush();
            $courts = $courtRepository->findAll();
        }

        return $this->render('reservation/index.html.twig', [
            'courts' => $courts,
        ]);
    }

    #[Route('/api/booked-slots', name: 'api_booked_slots', methods: ['GET'])]
    public function getBookedSlots(Request $request, CourtRepository $courtRepository, ReservationRepository $reservationRepository): JsonResponse
    {
        $courtId = (int)$request->query->get('court_number');
        $dateStr = $request->query->get('reservation_date');

        if (!$courtId || !$dateStr) {
            return new JsonResponse(['success' => false, 'bookedSlots' => []]);
        }

        $court = $courtRepository->find($courtId);
        if (!$court) {
            return new JsonResponse(['success' => false, 'bookedSlots' => []]);
        }

        $date = new \DateTime($dateStr);
        $reservations = $reservationRepository->findBy([
            'court' => $court,
            'reservationDate' => $date
        ]);

        $bookedSlots = [];
        foreach ($reservations as $res) {
            $bookedSlots[] = $res->getReservationTime()->format('H:i');
        }

        return new JsonResponse(['success' => true, 'bookedSlots' => $bookedSlots]);
    }

    #[Route('/api/reservation/submit', name: 'app_reservation_submit', methods: ['POST'])]
    public function submit(
        Request $request,
        CourtRepository $courtRepository,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'loginRequired' => true,
                'message' => 'Vous devez être connecté pour réserver un terrain.'
            ], 401);
        }

        $courtId = (int)$request->request->get('court_number');
        $dateStr = (string)$request->request->get('reservation_date');
        $timeStr = (string)$request->request->get('reservation_time');

        $court = $courtRepository->find($courtId);
        if (!$court) {
            return new JsonResponse(['success' => false, 'message' => 'Terrain introuvable.']);
        }

        try {
            $date = new \DateTime($dateStr);
            $time = new \DateTime($timeStr);

            $existingReservation = $reservationRepository->findOneBy([
                'court' => $court,
                'reservationDate' => $date,
                'reservationTime' => $time
            ]);

            if ($existingReservation) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Désolé, ce créneau vient tout juste d\'être réservé par un autre joueur !'
                ]);
            }

            $reservation = new Reservation();
            $reservation->setUser($user);
            $reservation->setCourt($court);
            $reservation->setReservationDate($date);
            $reservation->setReservationTime($time);
            $reservation->setPlayerCount((int)$request->request->get('player_count', 4));
            $reservation->setRequirements($request->request->get('requirements') ?: null);
            $reservation->setCreatedAt(new \DateTimeImmutable());
            
            if (method_exists($reservation, 'setPrice')) {
                $reservation->setPrice(100.00);
            }

            $entityManager->persist($reservation);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Réservation confirmée avec succès !'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Une erreur interne est survenue lors de la création de la réservation.'
            ], 500);
        }
    }
}