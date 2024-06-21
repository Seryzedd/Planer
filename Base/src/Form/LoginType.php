<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User\User;


class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('POST')
            ->add('userName', TextType::class, [
                    'label' => 'Username',
                    'empty_data' => '',
                    'attr' => [
                        'placeholder' => 'My username',
                        'class' => 'text-center'
                    ]
                ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => [
                        'placeholder' => 'Password anthentication',
                        'class' => 'text-center'
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}