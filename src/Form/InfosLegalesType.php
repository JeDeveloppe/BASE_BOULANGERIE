<?php

namespace App\Form;

use App\Entity\InfosLegales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfosLegalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('urlSite')
            ->add('nomSociete')
            ->add('siretSociete')
            ->add('adresseSociete')
            ->add('nomWebmaster')
            ->add('emailSite')
            ->add('societeWebmaster')
            ->add('hebergeur')
            ->add('tva', NumberType::class, [
                'label' => 'Multiplicateur de TVA:',
                'attr' => [
                    'placeholder' => 'exemple: 1.20 pour 20%'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfosLegales::class,
        ]);
    }
}
