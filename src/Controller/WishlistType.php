<?php
// filepath: c:\wrk\perso\webapp\symfony-docker\symfony-docker\project\src\Form\WishlistType.php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class WishlistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la liste',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom pour cette liste',
                    ]),
                ],
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date limite (optionnelle)',
                'widget' => 'single_text',
                'required' => false,
            ])
            // ->add('collaborators', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => function (User $user) {
            //         return $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getEmail() . ')';
            //     },
            //     'multiple' => true,
            //     'expanded' => false,
            //     'required' => false,
            //     'label' => 'Collaborateurs (optionnel)',
            // ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wishlist::class,
        ]);
    }
}