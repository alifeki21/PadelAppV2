<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\CourtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation', methods: ['GET'])]
    public function index(CourtRepository $courtRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'courts' => $courtRepository->findAll(),
        ]);
    }

    #[Route('/reservation', name: 'app_reservation_submit', methods: ['POST'])]
    public function submit(
        Request $request,
        CourtRepository $courtRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour réserver un terrain.');
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('reservation', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton de sécurité invalide.');
            return $this->redirectToRoute('app_reservation');
        }

        $court = $courtRepository->find((int) $request->request->get('court_id'));
        if (!$court) {
            $this->addFlash('error', 'Terrain introuvable.');
            return $this->redirectToRoute('app_reservation');
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setCourt($court);
        $reservation->setReservationDate(new \DateTime((string) $request->request->get('reservation_date')));
        $reservation->setReservationTime(new \DateTime((string) $request->request->get('reservation_time')));
        $reservation->setPlayerCount((int) $request->request->get('player_count', 4));
        $reservation->setRequirements($request->request->get('requirements') ?: null);
        $reservation->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation confirmée !');
        return $this->redirectToRoute('app_reservation');
    }
}
