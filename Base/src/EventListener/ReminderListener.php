<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CalendarEventRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Translation\TranslatableMessage;

final class ReminderListener
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly CalendarEventRepository $repository, private readonly Security $security) {}

    #[AsEventListener]
    public function onRequestEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $session = $request->getSession();

        $user = $this->security->getUser();

        if ($user) {
            $userId = $user->getId();

            $events = $this->repository->findMyCloseEvents($user);

            foreach ($events as $event) {
                $sessionId = 'reminder_event_' . $event->getId();
                
                $this->removeReminderFromSession($session, $sessionId);

                if ($session && !$session->has($sessionId)) {
                    // Logic to show reminder (e.g., flash message)
                    $session->getFlashBag()->add(
                        'info', 
                        new TranslatableMessage('You have an upcoming event "%eventName%" beginning at %eventTime% ending at %eventEndTime%', [
                            '%eventName%' => $event->getName(),
                            '%eventTime%' => $event->getStartAt()->format('d/m/Y H:i'),
                            '%eventEndTime%' => $event->getEndAt()->format('d/m/Y H:i'),
                        ])
                    );

                    
                    $this->setEventInSession($session, $sessionId, $event);
                }
            }
        }
    }

    private function setEventInSession($session, $sessionId, $event): void
    {
        $session->set($sessionId, [
            'event' => $event,
            'shownAt' => new \DateTime(),
        ]);
    }

    private function removeReminderFromSession($session, $sessionId): void
    {
        $reminder = $session->has($sessionId) ? $session->get($sessionId) : null;
        if ($reminder) {
            
            if (isset($reminder['shownAt']) && $reminder['shownAt'] > new \DateTime('+1 hour')) {
                $session->remove($sessionId);
            }

            if (!isset($reminder['shownAt'])) {
                $session->remove($sessionId);
            }

            if (isset($reminder['event'])) {
                $event = $reminder['event'];
                
                if ($event && $event->getEndAt() > new \DateTime()) {
                    $session->remove($sessionId);
                }
            }
        }
    }
}