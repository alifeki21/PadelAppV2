<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label'       => 'Prénom',
                'attr'        => ['placeholder' => 'Entrez votre prénom'],
                'constraints' => [
                    new NotBlank(message: 'Le prénom est requis.'),
                    new Length(
                        min: 2,
                        max: 50,
                        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.',
                    ),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label'       => 'Nom',
                'attr'        => ['placeholder' => 'Entrez votre nom'],
                'constraints' => [
                    new NotBlank(message: 'Le nom est requis.'),
                    new Length(
                        min: 2,
                        max: 50,
                        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
                    ),
                ],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'Adresse E-mail',
                'attr'        => ['placeholder' => 'Entrez votre adresse mail'],
                'constraints' => [
                    new NotBlank(message: 'L\'adresse e-mail est requise.'),
                    new Email(message: 'Veuillez entrer une adresse e-mail valide.'),
                ],
            ])
            ->add('phone', TelType::class, [
                'label'       => 'Numéro de téléphone',
                'attr'        => ['placeholder' => 'Entrez votre numéro de téléphone'],
                'constraints' => [
                    new NotBlank(message: 'Le numéro de téléphone est requis.'),
                    new Length(max: 20),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'first_options'   => [
                    'label' => 'Mot de passe',
                    'attr'  => ['placeholder' => 'Créez un mot de passe', 'autocomplete' => 'new-password'],
                ],
                'second_options'  => [
                    'label' => 'Confirmer le mot de passe',
                    'attr'  => ['placeholder' => 'Confirmez votre mot de passe', 'autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints'     => [
                    new NotBlank(message: 'Le mot de passe est requis.'),
                    new Length(
                        min: 8,
                        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ),
                ],
            ])
            ->add('skillLevel', NumberType::class, [
                'label'       => 'Niveau de compétence (1–10)',
                'required'    => false,
                'scale'       => 1,
                'attr'        => ['placeholder' => 'Ex: 5.5', 'min' => 1, 'max' => 10, 'step' => 0.1],
                'constraints' => [
                    new Range(
                        min: 1,
                        max: 10,
                        notInRangeMessage: 'Le niveau doit être compris entre {{ min }} et {{ max }}.',
                    ),
                ],
            ])
            ->add('preferredPosition', ChoiceType::class, [
                'label'       => 'Position préférée',
                'required'    => false,
                'placeholder' => 'Choisir une position',
                'choices'     => [
                    'Gauche'    => 'left',
                    'Droite'    => 'right',
                    'Les deux'  => 'both',
                ],
            ])
            ->add('playingHand', ChoiceType::class, [
                'label'       => 'Main de jeu',
                'required'    => false,
                'placeholder' => 'Choisir une main',
                'choices'     => [
                    'Droitier' => 'right',
                    'Gaucher'  => 'left',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped'      => false,
                'label'       => "J'accepte les Conditions d'utilisation et la Politique de confidentialité",
                'constraints' => [
                    new IsTrue(message: 'Vous devez accepter les conditions d\'utilisation.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
