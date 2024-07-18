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
use App\Entity\Work\Assignation;

#[Route('/Calendar')]
class CalendarController extends BaseController
{
    #[Route('/', name: 'app_calendar_index')]
    #[Route('/{month}/{year}', name: 'app_calendar_month')]
    public function index(Request $request, ?string $month, ?string $year): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        $entityManager = $this->entityManager;
        $assignation = new Assignation();

        if ($request->get('action') === "Update") {
            $updatedAssignation = $entityManager->getRepository(Assignation::class)->find($request->get('assignation_id'));

            $date = \DateTime::createFromFormat('d/m/Y', $request->get('startDate'));

            $updatedAssignation->setStartAt($date);

            if ($request->get('duration')) {
                $updatedAssignation->setDuration($request->get('duration'));
            }

            if ($request->get('deadline')) {
                $date = \DateTime::createFromFormat('Y-m-d', $request->get('deadline'));
                $updatedAssignation->setDeadline($date);
            }
            
            $updatedAssignation->setHalfDay($request->get('halfDay'));

            $entityManager->persist($updatedAssignation);
            $entityManager->flush();
        }

        if ($request->get('action')) {
            if ($request->get('user')) {
                $user = $entityManager->getRepository(User::class)->find($request->get('user'));
            }

            if ($request->get('project')) {
                $project = $entityManager->getRepository(Project::class)->find($request->get('project'));
            }

            if ($request->get('action') === "new") {
                $project = new Project();

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
                }
                
                $assignation->setStartAt($startDate);
                
                $assignation->setHalfDay($request->get('halfDay'));

                $project->addAssignation($assignation);
                $project->setClient($client);
                
                $entityManager->persist($project);
                $entityManager->persist($assignation);
                $entityManager->flush();
            } elseif ($request->get('action') === "select") {
                
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
            } elseif ($request->get('action') === 'rename') {
                if ($request->get('project')) {
                    $project = $entityManager->getRepository(Project::class)->find($request->get('project'));
                }

                if ($request->get('name')) {
                    $project->setName($request->get('name'));
                }

                $entityManager->persist($project);
                $entityManager->flush();
            }
        }

        $entityManager->clear();
        
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $users = $entityManager->getRepository(User::class)->findAll();

            $clients = $entityManager->getRepository(Client::class)->findAll();
        } else {
            $users = $entityManager
                ->getRepository(User::class)
                ->createQueryBuilder('user', 'user.email')
                ->where('user.company = ' . $this->getUser()->getCompany()->getId())
                ->getQuery()
                ->getResult()
            ;

            $clients = $entityManager->getRepository(Client::class)->findBy(['companyId' => $this->getUser()->getCompany()->getId()]);
        }
        

        return $this->render('Calendar/index.html.twig', [
            'users' => $users,
            'month' => $month,
            'year' => $year,
            'clients' => $clients
        ]);
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