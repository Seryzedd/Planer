<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\LocaleSwitcher;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\Security;

final class TranslateLocaleListener
{
    private LocaleSwitcher $switcher;

    public function __construct(LocaleSwitcher $switcher, private readonly CacheItemPoolInterface $cacheItemPool, private readonly Security $security) {
        $this->switcher = $switcher;
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $session = $request->getSession();

        $locale = $request->getDefaultLocale();
        
        if ($request->get('locale_language')) {
            $locale = $request->get('locale_language');

            $session->set('_language', $locale);
        } else {
            $user = $this->security->getUser();
            if ($session->get('_language')) {

                $locale = $session->get('_language');
            } elseif ($user) {
                $country = $user->getCompany()->getCountry();

                if ($country === "FR") {
                    $locale = $country;
                }
            }
        }

        $request->setLocale($locale);

        $this->switcher->setLocale($locale);
    }
}
