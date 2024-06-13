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
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * admin controller
 */
#[Route('/admin')]
class AdminController extends BaseController
{

    /**
     * 
     */
    #[Route('/', name: 'admin_index')]
    public function index(UserRepository $userRepo, ClientRepository $clientRepo, AbsenceRepository $absenceRepo, ProjectRepository $projectRepo, TeamRepository $teamRepo): Response
    {
        return $this->render('admin/index.html.twig', [
            'Users' => $userRepo->findByCompany($this->getUser()->getCompany()->getId()),
            'Clients' => $clientRepo->findByCompany($this->getUser()->getCompany()->getId()),
            'absences' => $absenceRepo->findAllByCompany($this->getUser()->getCompany()->getId()),
            "projects" => $projectRepo->findByCompany($this->getUser()->getCompany()->getId()),
            "teams" => $teamRepo->findByCompany($this->getUser()->getCompany()->getId()),
        ]);
    }
}