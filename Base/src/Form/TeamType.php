<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Client\Project;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use App\Entity\Client\Client;
use App\Entity\User\Team;
use App\Entity\User\User;
use Symfony\Component\Form\ChoiceList\ChoiceList;

class TeamType extends AbstractType
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
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control text-center'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control text-center'
                ],
                "empty_data" => ''
            ])
            ->add('lead', EntityType::class, [
                'class' => User::class,
                'attr' => [
                    'class' => 'form-control text-center'
                ],
                'choices' => $user->getCompany() ? $this->repository->findByCompany($user->getCompany()->getId()) : $this->repository->findAll(),
                'choice_label' => 'username'
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_attr' => ChoiceList::attr($this, function (?User $team): array {
                    return ['class' => 'form-control mx-auto'];
                }),
                'row_attr' => ['class' => ' mx-2'],
                'attr' => ['class' => 'd-flex justify-content-center flex-wrap'],
                'group_by' => 'job',
                'multiple' => true,
                'expanded' => true,
                'choices' => $user->getCompany() ? $this->repository->findByCompany($user->getCompany()->getId()) : $this->repository->findAll(),
                'choice_label' => 'username',
                'label' => 'Members',
                'allow_extra_fields' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class
        ]);
    }
}