<?php

namespace App\Form;

use App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            'data_class' => ContactMessage::class,
        ]);
    }
}
