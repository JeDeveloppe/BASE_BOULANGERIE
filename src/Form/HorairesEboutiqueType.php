<?php

namespace App\Form;

use App\Entity\HorairesEboutique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HorairesEboutiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day', ChoiceType::class, [
                'label' => 'Jour',
                'choices' => [
                    'Lundi'     => 'LUNDI',
                    'Mardi'     => 'MARDI',
                    'Mercredi'  => 'MERCREDI',
                    'Jeudi'     => 'JEUDI',
                    'Vendredi'  => 'VENDREDI',
                    'Samedi'    => 'SAMEDI',
                    'Dimanche'  => 'DIMANCHE'
                ],
                'required' => true
            ])
            ->add('ouvertureMatin', TextType::class, [
                'label' => 'Horaire d\'ouverture'
            ])
            ->add('fermetureSoir', TextType::class, [
                'label' => 'Horaire de fermeture'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HorairesEboutique::class,
        ]);
    }
}
