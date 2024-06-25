<?php

namespace App\Form;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($builder->getData()->getEmail() === "") {
            $builder->add('email', EmailType::class, []);
        }
        $builder
            ->add('username', TextType::class, [
                'help' => 'This username is the name I showed on my profile. This does not have to be real.',
                'help_attr' => ['class' => 'text-muted']
            ])
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
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
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
