<?php

namespace App\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User\User;
use App\Entity\Client\Client;
use App\Entity\Client\Project;
use App\Entity\HoursSold;
use App\Entity\Work\Assignation;
use App\Repository\UserRepository;

#[Route('/Calendar')]
class CalendarController extends BaseController
{
    #[Route('/', name: 'app_calendar_index')]
    #[Route('/{month}/{year}', name: 'app_calendar_month')]
    public function index(Request $request, ?string $month, ?string $year, UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        $entityManager = $this->entityManager;

        if ($request->get('action') === "Update") {
            $this->update($request);
        }
        elseif ($request->get('action')  === "new") {   
            $this->create($request);
        }
        elseif ($request->get('action') === "select") {
            $this->select($request);
        }
        elseif ($request->get('action') === 'rename') {
            $this->rename($request);
        }

        $entityManager->clear();
        
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $users = $entityManager->getRepository(User::class)->findAll();

            $clients = $entityManager->getRepository(Client::class)->findAll();
        } else {
            $users = $userRepository->findByCompany($this->getUser()->getCompany()->getId());

            $clients = $entityManager->getRepository(Client::class)->findBy(['companyId' => $this->getUser()->getCompany()->getId()]);
        }

        return $this->render('Calendar/index.html.twig', [
            'users' => $users,
            'month' => $month,
            'year' => $year,
            'clients' => $clients
        ]);
    }

    private function update(Request $request): void
    {
        $entityManager = $this->entityManager;

        $updatedAssignation = $entityManager->getRepository(Assignation::class)->find($request->get('assignation_id'));

        $date = \DateTime::createFromFormat('d/m/Y', $request->get('startDate'));

        $date->setTime(0,0);
        
        $updatedAssignation->setStartAt($date);

        if ($request->get('duration')) {
            $updatedAssignation->setDuration($request->get('duration'));
        }

        if ($request->get('deadline')) {
            $date = \DateTime::createFromFormat('d/m/Y', $request->get('deadline'));
            $updatedAssignation->setDeadline($date);
        }
        
        $updatedAssignation->setHalfDay($request->get('halfDay'));

        $entityManager->persist($updatedAssignation);
        $entityManager->flush();
    }

    private function create(Request $request): void
    {
        $project = new Project();
        $assignation = new Assignation();

        $entityManager = $this->entityManager;

        if ($request->get('user')) {
            $user = $entityManager->getRepository(User::class)->find($request->get('user'));
        }

        if ($request->get('project')) {
            $project = $entityManager->getRepository(Project::class)->find($request->get('project'));
        }

        $client = $entityManager->getRepository(Client::class)->find($request->get('client'));

        $assignation->setUser($user);

        if ($request->get('startDate')) {
            $startDate = \DateTime::createFromFormat('Y/m/d', $request->get('startDate'));
        } else {
            $startDate = new \DateTime();
        }

        if ($request->get('deadline')) {
            $date = \DateTime::createFromFormat('Y/m/d', $request->get('deadline'));
            $assignation->setDeadline($date);
            $assignation->setDuration(1);
        } else {
            $assignation->setDeadline(null);
            $assignation->setDuration($request->get('duration'));

            $hours = new HoursSold();
            $hours->setDelay($request->get('duration'));
            $hours->setType($user->getJob());

            $entityManager->persist($hours);

            $project->addHoursSold($hours);
        }
        
        $assignation->setStartAt($startDate);
        
        $assignation->setHalfDay($request->get('halfDay'));

        $project->addAssignation($assignation);
        $project->setClient($client);
        
        $entityManager->persist($project);
        $entityManager->persist($assignation);
        $entityManager->flush();
    }

    private function select(Request $request): void
    {
        $entityManager = $this->entityManager;
        $project = $entityManager->getRepository(Project::class)->find($request->get('project'));
        $assignation = new Assignation();
        
        if ($request->get('user')) {
            $user = $entityManager->getRepository(User::class)->find($request->get('user'));
        }

        if ($request->get('startDate')) {
                $startDate = \DateTime::createFromFormat('Y/m/d', $request->get('startDate'));
            } else {
                $startDate = new \DateTime();
            }

            if ($request->get('deadline')) {
                $date = \DateTime::createFromFormat('Y/m/d', $request->get('deadline'));
                $assignation->setDeadline($date);
                $assignation->setDuration(1);
            } else {
                $assignation->setDeadline(null);
                $assignation->setDuration($request->get('duration'));
            }

            $assignation->setUser($user);
            $assignation->setStartAt($startDate);
            $assignation->setHalfDay($request->get('halfDay'));

            $project->addAssignation($assignation);

            $entityManager->persist($assignation);
            $entityManager->persist($project);
            $entityManager->flush();
    }

    private function rename(Request $request): void
    {
        $entityManager = $this->entityManager;

        if ($request->get('project')) {
            $project = $entityManager->getRepository(Project::class)->find($request->get('project'));
        }

        if ($request->get('name')) {
            $project->setName($request->get('name'));
        }

        $entityManager->persist($project);
        $entityManager->flush();
    }

    #[Route('assignation/{id}/', name: 'app_calendar_assignation_base_delete')]
    #[Route('assignation/{id}/{month}/{year}', name: 'app_calendar_assignation_delete')]
    public function deleteAssignation(Assignation $assignation, ?string $month, ?string $year)
    {
        $entityManager = $this->entityManager;
        
        $entityManager->remove($assignation);
        $entityManager->flush();
        
        if ($month && $year) {
            return $this->redirectToRoute('app_calendar_month', ['year' => $year, 'month' => $month]);
        }

        return $this->redirectToRoute('app_calendar_index', []);
    }
}