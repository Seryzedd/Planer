<?php

namespace App\Form;

use App\Entity\Client\Project;
use App\Entity\HoursSold;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HoursSoldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => User::JOBS
            ])
            ->add('delay', NumberType::class, [
                'attr' => [
                    'step' => '0.5',
                ],
                'html5' => true,
                'data' => 1
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'id',
                'attr' => [
                    'class' => 'd-none',
                    'input-parent-target' => 'project'
                ],
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HoursSold::class,
        ]);
    }
}
