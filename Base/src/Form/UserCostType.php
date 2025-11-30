<?php

namespace App\Form;

use App\Entity\Cost;
use App\Entity\User\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;

class UserCostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', NumberType::class, [
                'row_attr' => [
                    'class' => 'w-50 px-2 price-p-hour'
                ],
                'input' => 'number',
                'html5' => true,
                'scale' => 2,
                'label' => 'Price per hour',
                'attr' => [
                    'type' => 'number'
                ]
            ])
            ->add('startAt', DateType::class, [
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'placeholder' => '_ _/_ _/_ _ _ _',
                    'class' => 'date'
                ],
                'html5' => false,
                'row_attr' => [
                    'class' => 'w-50 px-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cost::class,
            'attr' => [
                    'class' => 'd-flex align-items-center justify-content-stretch'
            ],
        ]);
    }
}
