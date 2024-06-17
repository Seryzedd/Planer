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
use App\Repository\ClientRepository;
use Symfony\Component\Security\Core\Security;
use App\Entity\Client\Client;

class ProjectType extends AbstractType
{

    private ClientRepository $repository;

    public function __construct(private readonly Security $security, ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        
        $clients = $user->getCompany() ? $this->repository->findByCompany($user->getCompany()->getId()) : $this->repository->findAll();
        
        $builder
            ->add('name', TextType::class, [])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => $clients,
                'choice_label' => 'name'
            ])
            ->add('deadline', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
                'empty_data' => null,
                'attr' => ['class' => '']
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'empty_data' => ''
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class
        ]);
    }
}