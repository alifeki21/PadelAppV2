<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\User;
use App\Form\ContactMessageType;
use App\Repository\ContactMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY', message: 'Vous devez être connecté pour envoyer un message.')]
    public function index(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $contactMessage = new ContactMessage();

        $form = $this->createForm(ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactMessage->setUser($user);
            $contactMessage->setCreatedAt(new \DateTimeImmutable());

            $em->persist($contactMessage);
            $em->flush();

            $this->addFlash(
                'success',
                'Merci ! Votre message a bien été envoyé. Nous vous répondrons rapidement.'
            );

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/contact/mes-messages', name: 'app_contact_my_messages', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function myMessages(ContactMessageRepository $repository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $messages = $repository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('contact/my_messages.html.twig', [
            'messages' => $messages,
        ]);
    }
}
