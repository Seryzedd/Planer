<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\CalendarEvent;
use App\Form\CalendarEventType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Controller\BaseController;

#[Route('/Event')]
final class CalendarEventController extends BaseController
{
    #[Route('/', name: 'app_calendar_event')]
    public function index(): Response
    {
        return $this->render('calendar_event/index.html.twig', []);
    }

    #[Route('/{id}/update', name: 'app_calendar_event_update')]
    public function update(CalendarEvent $id, Request $request): Response
    {
        $form = $this->getEventForm($id, $request, 'Update');

        if (!$form) {
            return $this->redirectToRoute('app_calendar_event');
        }

        return $this->render('calendar_event/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_calendar_event_create')]
    public function create(Request $request): Response
    {
        $event = new CalendarEvent();

        $form = $this->getEventForm($event, $request);

        if (!$form) {
            return $this->redirectToRoute('app_calendar_event');
        }

        return $this->render('calendar_event/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'app_calendar_event_delete')]
    public function delete(Request $request, CalendarEvent $id)
    {
        $entityManager = $this->entityManager;

        $entityManager->remove($id);

        $entityManager->flush();

        $this->addFlash('success', 'Event deleted');

        return $this->redirectToRoute('app_calendar_event');
    }

    private function getEventForm(CalendarEvent $event, Request $request, string $button = "Create")
    {
        $form = $this->createForm(CalendarEventType::class, $event, ['company' => $this->getUser()->getCompany()]);

        $form
            ->add('create', SubmitType::class, [
                'label' => $button,
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($event->getStartAt() > $event->getEndAt()) {
                $this->addFlash('danger', 'Start date can\t be greater than end date.');
            } else {
                $entityManager = $this->entityManager;

                $entityManager->persist($form->getData());
                $entityManager->flush();

                if ($button === "Create") {
                    $this->addFlash('success', 'Event created.');
                } elseif ($button === "Update") {
                    # code...
                } {
                    $this->addFlash('success', 'Event updated.');
                }

                return false;
            }
        }

        return $form;
    }
}
