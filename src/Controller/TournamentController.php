<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\TournamentRegistration;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TournamentController extends AbstractController
{
    #[Route('/tournaments', name: 'app_tournaments_index', methods: ['GET'])]
    public function index(TournamentRepository $tournamentRepository): Response
    {
        // Fetch upcoming tournaments
        $tournaments = $tournamentRepository->findBy(
            ['status' => 'upcoming'],
            ['startDate' => 'ASC']
        );

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournaments,
        ]);
    }

    #[Route('/tournaments/inscription', name: 'app_tournaments_inscription', methods: ['GET'])]
    public function inscription(TournamentRepository $tournamentRepository): Response
    {
        $tournaments = $tournamentRepository->findBy(
            ['status' => 'upcoming'],
            ['startDate' => 'ASC']
        );

        return $this->render('tournament/inscription.html.twig', [
            'tournaments' => $tournaments,
        ]);
    }

    #[Route('/tournaments/{id}/register', name: 'app_tournaments_register', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function register(Tournament $tournament, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour vous inscrire à un tournoi.');
            return $this->redirectToRoute('app_login'); // assuming app_login exists
        }

        $currentTeams = count($tournament->getTournamentRegistrations());
        if ($tournament->getMaxTeams() !== null && $currentTeams >= $tournament->getMaxTeams()) {
            $this->addFlash('error', 'Ce tournoi est déjà complet.');
            return $this->redirectToRoute('app_tournaments_index');
        }

        if ($request->isMethod('POST')) {
            $partnerName = $request->request->get('partner_name');

            $registration = new TournamentRegistration();
            $registration->setTournament($tournament);
            $registration->setUser($user);
            $registration->setPartnerName($partnerName);
            $registration->setRegisteredAt(new \DateTimeImmutable());

            $entityManager->persist($registration);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription au tournoi réussie !');
            return $this->redirectToRoute('app_tournaments_index');
        }

        return $this->render('tournament/register.html.twig', [
            'tournament' => $tournament,
        ]);
    }
}
