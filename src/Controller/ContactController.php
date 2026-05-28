<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig');
    }

    #[Route('/contact', name: 'app_contact_submit', methods: ['POST'])]
    public function submit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour envoyer un message.');
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('contact', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton de sécurité invalide.');
            return $this->redirectToRoute('app_contact');
        }

        $subject = trim((string) $request->request->get('subject'));
        $message = trim((string) $request->request->get('message'));

        if ($subject === '' || $message === '') {
            $this->addFlash('error', 'Le sujet et le message sont obligatoires.');
            return $this->redirectToRoute('app_contact');
        }

        $contactMessage = new ContactMessage();
        $contactMessage->setUser($user);
        $contactMessage->setSubject($subject);
        $contactMessage->setMessage($message);
        $contactMessage->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($contactMessage);
        $entityManager->flush();

        $this->addFlash('success', 'Votre message a bien été envoyé. Merci !');
        return $this->redirectToRoute('app_contact');
    }
}
