<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Client\Client;
use App\Repository\ClientRepository;

class ClientChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        dump($options); die;
        $builder
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
            'data_class' => Client::class,
            'repository' => ClientRepository::class
        ]);
    }
}