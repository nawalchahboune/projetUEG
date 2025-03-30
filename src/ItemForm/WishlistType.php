<?php

namespace App\ItemForm;

use App\Entity\Wishlist;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                'label'  => 'Date limite (optionnelle)',
                'widget' => 'single_text',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wishlist::class,
            'include_collaborators' => false, // cette option n'a plus d'effet
        ]);
    }
}
