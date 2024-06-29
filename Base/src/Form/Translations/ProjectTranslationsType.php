<?php

namespace App\Form\Translations;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Client\Project;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\CallbackTransformer;

class ProjectTranslationsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder->add('translations', CollectionType::class, [
            // each entry in the array will be an "email" field
            'entry_type' => ProjectTranslationType::class,
            // these options are passed to each "email" type
            'entry_options' => [
                'translation_domain' => false,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class
        ]);
    }
}