<?php

namespace App\Controller;

use App\Entity\User\User;
use App\Entity\User\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Invitation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client\Client;
use App\Entity\Client\Project;
use App\Entity\Work\Assignation;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Repository\AbsenceRepository;
use App\Repository\ProjectRepository;
use App\Repository\TeamRepository;
use App\Repository\CompanyRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Service\CostsCalculator;

/**
 * admin controller
 */
#[Route('/admin')]
class AdminController extends BaseController
{

    /**
     * 
     */
    #[Route('/', name: 'admin_index', defaults: ['admin' => true, 'title' => 'Admin homepage', 'icon' => 'door-open', 'role' => 'ROLE_ADMIN'])]
    public function index(UserRepository $userRepo, ClientRepository $clientRepo, AbsenceRepository $absenceRepo, ProjectRepository $projectRepo, TeamRepository $teamRepo, CostsCalculator $costsCalculator): Response
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('admin_company_list');
        }

        $clients = $clientRepo->findByCompany($this->getUser()->getCompany()->getId());

        return $this->render('admin/index.html.twig', [
            'Users' => $userRepo->findByCompany($this->getUser()->getCompany()->getId()),
            'Clients' => $clients,
            'absences' => $absenceRepo->findAllByCompany($this->getUser()->getCompany()->getId()),
            "projects" => $projectRepo->findByCompany($this->getUser()->getCompany()->getId()),
            "teams" => $teamRepo->findByCompany($this->getUser()->getCompany()->getId()),
            'graphics' => [
                'clients' => $costsCalculator->calculateTotalCostsClients($clients)
            ]
        ]);
    }

    /**
     * @var CompanyRepository $companyRepository
     */
    #[Route('/Company/list', name: 'admin_company_list')]
    public function companyList(CompanyRepository $companyRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {

            return $this->redirectToRoute('admin_company_list');
        }

        return $this->render('admin/Company/list.html.twig', [
            'companies' => $companyRepository->findAll()
        ]);
    }
}