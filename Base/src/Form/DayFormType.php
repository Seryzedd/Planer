<?php

namespace App\Form;

use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use function Sodium\add;

class DayFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hours = $this->getHoursChoices();
        $minutes = $this->getMinutesChoices();
        $builder
            ->add('startHour', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'choices' => $hours,
                'label' => false
                ])
            ->add('startMinutes', ChoiceType::class, [
                'attr' => ['class' => 'form-control mr-2'],
                'label' => false,
                'choices' => $minutes
            ])
            ->add('endHour', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => false,
                'choices' => $hours
            ]
            )
            ->add('endMinutes', ChoiceType::class, [
                    'choices' => $minutes,
                    'label' => false,
                    'attr' => ['class' => 'form-control mr-2']
                ]
                )
            ->add('working', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'style' => 'max-width:50px;',
                    'class' => 'form-control mr-2',
                    'inputmode' => "numeric"
                    ]
                ])
        ;
    }

    private function getHoursChoices()
    {
        $hours = [];

        $hour = 0;

        while($hour <= 24)
        {
            $hours[(string) $hour] = (string) $hour;
            $hour++;
        }

        return $hours;
    }

    private function getMinutesChoices()
    {
        $minutes = [
            (string) 0 => (string) 0,
            (string) 30 => (string) 30
        ];

        return $minutes;
    }
}