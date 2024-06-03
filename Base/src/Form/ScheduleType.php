<?php

namespace App\Form;

use App\Entity\User\AfternoonSchedule;
use App\Entity\User\MorningSchedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('morning', DayFormType::class, [
                'data_class' => MorningSchedule::class,
                'label' => false,
                'row_attr' => ['class' => 'text-editor'],
                'attr' => [
                    'class' => 'form-inline justify-content-center'
                    ]
                ])
            ->add('afternoon', DayFormType::class, ['data_class' => AfternoonSchedule::class, 'label' => false, 'attr' => ['class' => 'form-inline justify-content-center']])
        ;
    }
}