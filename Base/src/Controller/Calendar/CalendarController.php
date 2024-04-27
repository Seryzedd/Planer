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
        $entityManager = $this->entityManager;
        
        if ($request->get('action') === "Update") {
            $updatedAssignation = $entityManager->getRepository(Assignation::class)->find($request->get('assignation_id'));

            $date = \DateTime::createFromFormat('d/m/Y', $request->get('startDate'));

            $updatedAssignation->setStartAt($date   );
            $updatedAssignation->setDuration($request->get('duration'));

            $entityManager->persist($updatedAssignation);
            $entityManager->flush();
        }

        if ($request->get('user')) {
            if ($request->get('project')) {
                $project = $entityManager->getRepository(Project::class)->find($request->get('project'));

            } else {
                $project = new Project();

                $entityManager->persist($project);
                $entityManager->flush();
            }

            if ($request->get('client')) {
                $client = $entityManager->getRepository(Client::class)->find($request->get('client'));
                if (!$client) {
                    $client = new Client();
                }
            } else {
                $client = new Client();
            }
            
            $user = $entityManager->getRepository(User::class)->find($request->get('user'));

            $userAssignations = $user->getAssignations();
            $assignations = [];
            foreach ($userAssignations as $userAssignation) {
                if ($userAssignation->getProject()) {
                    $assignations[] = $userAssignation->getProject()->getId();
                }
                
            }

            if (!in_array($project->getId(), $assignations)) {
                $assignation = new Assignation();
                $assignation->setUser($user);

                if ($request->get('startDate')) {
                    $startDate = \DateTime::createFromFormat('d/m/Y', $request->get('startDate'));
                } else {
                    $startDate = new \DateTime();
                }
                $assignation->setStartAt($startDate);
                $assignation->setDuration($request->get('duration'));

                $project->addAssignation($assignation);

                $client->setCompanyId($this->getUser()->getCompany()->getId());
                $client->addProject($project);

                $entityManager->persist($project);
                $entityManager->persist($client);
                $entityManager->persist($assignation);
                $entityManager->persist($user);

                $entityManager->flush();
            } else {
                $this->addFlash(
                   'warning',
                   'This user already have this project assigned.'
                );
            }
            
        }

        $entityManager->clear();
        
        $users = $entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('user', 'user.email')
            ->where('user.company = ' . $this->getUser()->getCompany()->getId())
            ->getQuery()
            ->getResult()
        ;

        return $this->render('Calendar/index.html.twig', [
            'users' => $users,
            'month' => $month,
            'year' => $year,
            'clients' => $entityManager->getRepository(Client::class)->findBy(['companyId' => $this->getUser()->getCompany()->getId()])
        ]);
    }
}