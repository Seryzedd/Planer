<?php

namespace App\Form;

use App\Entity\CalendarConfigurations;
use App\Entity\Company;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('useCounter', ChoiceType::class, [
                'choices' => [
                    'Use counter' => true,
                    'Use deadline date' => false
                ],
                'label' => 'Calendar parameter Type',
                'expanded' => true,
                'multiple' => false,
                'help' => 'Using Deadline means Assignation ends on deadline date, this ending is not updated if user not working on changing schedules or have absence.',
                'help_attr' => [
                    'class' => 'text-muted'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CalendarConfigurations::class,
        ]);
    }
}
