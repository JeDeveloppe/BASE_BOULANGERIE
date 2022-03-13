<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isOnLine', CheckboxType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('name', TextType::class, [
                'label' => false
            ])
            ->add('slug', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Espaces remplacés par -'
                ]
            ])
            ->add('prix', MoneyType::class, [
                'label' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => false
            ])
            ->add('categorie', EntityType::class, [
                'label' => false,
                'class' => Categorie::class,
                'required' => false
            ])
            ->add('imageBlob', FileType::class, [
                'label' => false,
                'required' => false,
                'data_class' => null,
                'mapped' => false
            ])
            // ->add('createdAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
