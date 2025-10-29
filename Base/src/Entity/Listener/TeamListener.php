<?php

namespace App\Entity\Listener;

use App\Entity\User\Team;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

class TeamListener
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function postLoad(Team $team)
    {
        
        foreach($team->getTranslations() as $translation) {
            if ($translation->getLanguage() === $this->requestStack->getCurrentRequest()->getLocale()) {
                $team->setTranslation($translation);
            }
        }
    }
}