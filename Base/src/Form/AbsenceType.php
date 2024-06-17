<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Client\Project;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use App\Entity\Client\Client;
use App\Entity\User\Absence;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\CallbackTransformer;

class AbsenceType extends AbstractType
{

    private UserRepository $repository;

    public function __construct(private readonly Security $security, UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => Absence::ABSENCE_TYPE_LIST,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('from', TextType::class, [
                'attr' => [
                    'class' => '',
                    'placeholder' => 'dd/mm/YYYY'
                ]
            ])
            ->add('to', TextType::class, [
                'attr' => [
                    'class' => '',
                    'placeholder' => 'dd/mm/YYYY'
                ]
            ])
        ;

        $builder
            ->get('from')
            ->addModelTransformer(new CallbackTransformer(
                function (?\DateTime $date): string {
                    if (!$date) {
                        $date = new \DateTime();
                    }
                    
                    return $date->format('d/m/Y');
                },
                function (?string $tagsAsDateTime): \DateTime {
                    
                    return \DateTime::createFromFormat('d/m/Y', $tagsAsDateTime);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class
        ]);
    }
}