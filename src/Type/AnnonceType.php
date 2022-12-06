<?php

namespace App\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Titre de mon annonce',
            ])
            ->add('price', MoneyType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Prix de mon bien',
                'row_attr' => ['class' => 'd-flex flex-column my-3'],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "blabla",
                ],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Description de mon bien',

                'help' => 'Rédigez une jolie description pour avoir plus de chance de vendre votre bien !',
            ])
            ->add('premium', CheckboxType::class, [
                'required' => false,
            ])
            ->add('notes', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "si vous avez besoin de rajouter des informations pour la modération",
                ],
                'label_attr' => ['class' => 'form-label'],
                'row_attr' => ['class' => 'w-100 mt-5'],
                'required' => false,
                'mapped' => false,
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
                'label' => 'Poster mon annonce 💾',
                'row_attr' => ['class' => 'w-100 d-flex mt-5 justify-content-center align-items-center'],
            ]);
    }
}