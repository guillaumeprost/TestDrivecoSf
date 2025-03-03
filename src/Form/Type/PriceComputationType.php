<?php

namespace App\Form\Type;

use App\Entity\PriceComputation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceComputationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('from', DateTimeType::class, [
                'label' => 'From',
                'required' => true
            ])
            ->add('to', DateTimeType::class, [
                'label' => 'To',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PriceComputation::class
        ]);
    }
}