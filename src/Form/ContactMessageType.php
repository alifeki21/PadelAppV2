<?php

namespace App\Form;

use App\Entity\ContactMessage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ContactMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultUser = $options['default_user'] ?? null;

        $builder
            ->add('contactName', TextType::class, [
                'label'       => 'Nom',
                'mapped'      => false,
                'data'        => $defaultUser instanceof User ? trim($defaultUser->getFirstName() . ' ' . $defaultUser->getLastName()) : null,
                'attr'        => [
                    'placeholder' => 'Votre nom complet',
                    'class'       => 'form-control',
                    'maxlength'   => 100,
                ],
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire.'),
                    new Length(min: 2, max: 100, minMessage: 'Au moins {{ limit }} caractères.'),
                ],
            ])
            ->add('contactEmail', EmailType::class, [
                'label'       => 'E-mail',
                'mapped'      => false,
                'data'        => $defaultUser instanceof User ? $defaultUser->getEmail() : null,
                'attr'        => [
                    'placeholder' => 'votre@email.com',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(message: 'L\'email est obligatoire.'),
                    new Email(message: 'Adresse email invalide.'),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label'   => 'Type de message',
                'choices' => ContactMessage::TYPES,
                'attr'    => ['class' => 'form-control', 'id' => 'contact-type'],
            ])
            ->add('subject', TextType::class, [
                'label'       => 'Sujet',
                'attr'        => [
                    'placeholder' => 'Le sujet de votre message',
                    'class'       => 'form-control',
                    'maxlength'   => 200,
                ],
                'constraints' => [
                    new NotBlank(message: 'Le sujet est obligatoire.'),
                    new Length(min: 3, max: 200, minMessage: 'Le sujet doit faire au moins {{ limit }} caractères.'),
                ],
            ])
            ->add('rating', IntegerType::class, [
                'label'    => 'Évaluation globale (optionnel)',
                'required' => false,
                'attr'     => ['min' => 1, 'max' => 5, 'class' => 'form-control'],
            ])
            ->add('reportedUser', EntityType::class, [
                'class'        => User::class,
                'choice_label' => function (User $u): string {
                    return $u->getFirstName() . ' ' . $u->getLastName() . ' (' . $u->getEmail() . ')';
                },
                'label'        => 'Joueur à signaler',
                'placeholder'  => '— Choisissez un joueur —',
                'required'     => false,
                'attr'         => ['class' => 'form-control'],
            ])
            ->add('reportedPlayerName', TextType::class, [
                'label'    => '... ou saisissez son nom (si non listé)',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Nom et prénom du joueur',
                    'class'       => 'form-control',
                    'maxlength'   => 200,
                ],
            ])
            ->add('reportedPlayerPhone', TelType::class, [
                'label'    => 'Téléphone du joueur (optionnel)',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Numéro de téléphone',
                    'class'       => 'form-control',
                    'maxlength'   => 30,
                ],
            ])
            ->add('reportedCurrentLevel', NumberType::class, [
                'label'       => 'Niveau actuellement affiché',
                'scale'       => 1,
                'required'    => false,
                'attr'        => ['min' => 0, 'max' => 10, 'step' => 0.1, 'class' => 'form-control'],
                'constraints' => [
                    new Range(min: 0, max: 10, notInRangeMessage: 'Le niveau doit être entre {{ min }} et {{ max }}.'),
                ],
            ])
            ->add('reportedSuggestedLevel', NumberType::class, [
                'label'       => 'Niveau correct selon vous',
                'scale'       => 1,
                'required'    => false,
                'attr'        => ['min' => 0, 'max' => 10, 'step' => 0.1, 'class' => 'form-control'],
                'constraints' => [
                    new Range(min: 0, max: 10, notInRangeMessage: 'Le niveau doit être entre {{ min }} et {{ max }}.'),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label'       => 'Votre message',
                'attr'        => [
                    'placeholder' => 'Décrivez votre demande, question, ou suggestion...',
                    'class'       => 'form-control',
                    'rows'        => 6,
                ],
                'constraints' => [
                    new NotBlank(message: 'Le message ne peut pas être vide.'),
                    new Length(min: 10, minMessage: 'Le message doit faire au moins {{ limit }} caractères.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ContactMessage::class,
            'default_user' => null,
        ]);
        $resolver->setAllowedTypes('default_user', [User::class, 'null']);
    }
}
