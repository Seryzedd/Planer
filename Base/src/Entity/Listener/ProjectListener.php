<?php

namespace App\Entity\Listener;

use App\Entity\Client\Project;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

class ProjectListener
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function postLoad(Project $project)
    {
        $locale = "";

        foreach($project->getTranslations() as $translation) {
            if ($translation->getLanguage() === $this->requestStack->getCurrentRequest()->getLocale()) {
                $project->setCurrentTranslation($translation);
            }
        }
    }
}