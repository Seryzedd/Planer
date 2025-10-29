<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserInformationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [])
            ->add('firstName', TextType::class, [])
            ->add('lastName', TextType::class, [])
            ->add('job', ChoiceType::class, [
                'choices' => User::JOBS,
                'attr' => [
                    'class' => 'form-control'
                    ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'row_attr' => [
                    "class" => "form-group"
                ],
                'attr' => [
                    'class' => 'w-100 form-control'
                ],
                'label_attr' => [
                    'class' => 'w-100'
                ],
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'w-100 form-control'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirm password',
                    'attr' => [
                        'class' => 'w-100 form-control'
                    ]

                ],
            ])
            ->add('headshot', FileType::class, [
                'label' => 'Brochure (PDF file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                'attr' => [
                    'class' => 'd-none'
                ],

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/apng',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}