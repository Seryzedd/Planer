<?php

namespace App\Form;

use App\Entity\CalendarEvent;
use App\Entity\Company;
use App\Entity\User\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\QueryBuilder;
use App\Repository\UserRepository;

class CalendarEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $company = $options['company'];

        $builder
            ->add('StartAt', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'hours' => ['07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
                'minutes' => [00, 15, 30, 45 ],
                'time_label' => 'Hour',
                'with_minutes' => true
            ])
            ->add('endAt', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'hours' => ['07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
                'minutes' => [00, 15, 30, 45 ],
                'with_minutes' => true
            ])
            ->add('name')
            ->add('description')
            ->add('adress')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'participant',
                "expanded" => true,
                'multiple' => true,
                'query_builder' => function (UserRepository $er) use($company): QueryBuilder {
                    return $er->createQuery($company->getId());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CalendarEvent::class,
            'company' => null
        ]);
    }
}
