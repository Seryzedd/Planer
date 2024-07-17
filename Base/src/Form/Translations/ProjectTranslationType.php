<?php

namespace App\Form\Translations;

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
use App\Entity\Translations\ProjectTranslation;
use Symfony\Component\Form\ChoiceList\ChoiceList;


class ProjectTranslationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $builder
            ->add('name', TextType::class, [
                'help_attr' => ['class' => 'text-muted']
            ])
            ->add('description', TextareaType::class, [
                'help_attr' => ['class' => 'text-muted']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectTranslation::class
        ]);
    }
}