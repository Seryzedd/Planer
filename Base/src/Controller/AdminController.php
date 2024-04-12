<?php

namespace App\Controller;

use App\Entity\User\User;
use App\Entity\User\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Invitation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client\Client;
use App\Entity\Client\Project;
use App\Entity\Work\Assignation;

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
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    /**
     * 
     */
    #[Route('/users', name: 'admin_users')]
    public function users(): Response
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $users = $this->entityManager->getRepository(User::class)->findAll();
        } else {
            $users = $this->entityManager->getRepository(User::class)->findBy(['company' => $this->getUser()->getCompany()]);
        }

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * 
     */
    #[Route('/user/{id}/role/add/{role}', name: 'admin_user_add_role')]
    public function userAddRole(User $user, string $role): Response
    {

        if (!in_array($role, User::ROLE_LIST)) {
            $this->addFlash('danger', 'This role does not exist.');
        } else {
            $user->addRole($role);

            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $user->getUsername() . ' has ' . strtolower(str_replace("_", ' ', $role)) . '.');
        }
        
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/{id}/role/remove/{role}', name: 'admin_user_remove_role')]
    public function removeRole(User $user, string $role)
    {
        if (!in_array($role, User::ROLE_LIST)) {
            $this->addFlash('danger', 'This role does not exist.');
        } else {
            $user->removeRole($role);

            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', strtolower(str_replace("_", ' ', $role)) . ' remove to ' . $user->getUsername() . '.');
        }

        return $this->redirectToRoute('admin_users');
    }

    /**
     * 
     */
    #[Route('/projects', name: 'admin_projects')]
    public function projects(): Response
    {
        $entityManager = $this->entityManager;

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $projects = $this->entityManager->getRepository(Project::class)->findAll();
        } else {
            $projects = $entityManager
                ->getRepository(Project::class)
                ->createQueryBuilder('project')
                ->join('project.client', 'client')
                ->where('client.companyId = ' . $this->getUser()->getCompany()->getId())
                ->getQuery()
                ->getResult()
            ;
        }

        return $this->render('admin/Projects/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/{id}', name: 'admin_project_update')]
    public function updateProject(Request $request, Project $project)
    {

        $project->getClient()->setName($request->get('clientName') ?: "");
        $project->setDescription($request->get('projectDescription') ?: "");
        $project->setName($request->get('projectName') ?: "");

        $entityManager = $this->entityManager;

        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('admin_projects');
    }

    #[Route('/projects/assignation/{id}', name: 'admin_assignation_update')]
    public function updateAssignation(Request $request, Assignation $assignation)
    {
        
        $assignation->setStartAt(new \DateTime($request->get('startDate')));
        
        $assignation->setDuration($request->get('duration'));

        $entityManager = $this->entityManager;

        $entityManager->persist($assignation);
        $entityManager->flush();

        $this->addFlash('success', 'Assignation updated.');

        return $this->redirectToRoute('admin_projects');
    }

    #[Route('/invitations', name: 'admin_invitations')]
    public function invitations(Request $request)
    {
        $newInvitation = new Invitation();

        $formBuilder = $this->createFormBuilder($newInvitation, [
            'attr' => [
                'class' => 'form-inline'
            ]
        ]);

        $formBuilder
            ->add('email', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Dupont@contact.fr'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'New',
                'attr' => [
                    'class' => 'btn btn-outline-primary'
                ]
            ])
        ;

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        $entityManager = $this->entityManager;
        if (!$this->getUser()->getCompany()) {
            $this->addFlash('info', 'You can\'t create new invitation without company.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if ($user->getCompany()) {

                $querybuilder = $entityManager->getRepository(Invitation::class)->createquerybuilder('p');
                $query = $querybuilder
                    ->where($querybuilder->expr()->gte('p.date', ':from'))
                    ->andWhere($querybuilder->expr()->gte('p.email', ':email'))
                    ->setparameter('from', new \DateTime('-1 day'))
                    ->setparameter('email', $newInvitation->getEmail())
                    ->getquery();

                if (empty($query->getResult())) {
                    $newInvitation->setCompany($user->getCompany());

                    $entityManager->persist($newInvitation);
                    $entityManager->flush();

                    $this->addFlash(
                       'success',
                       'Invitation create'
                    );
                } else {
                    $this->addFlash(
                        'danger',
                        'Valid invitation with same email already exist.'
                     );
                }
            }
            
        }

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $invitations = $this->entityManager->getRepository(invitation::class)->findAll();
        } else {
            $invitations = $this->entityManager->getRepository(invitation::class)->findBy(['company' => $this->getUser()->getCompany()]);
        }
        
        return $this->render('admin/invitations/index.html.twig', [
            'invitations' => $invitations,
            'form' => $form->createView()
        ]);
    }

    #[Route('/teams', name: 'admin_teams')]
    public function getTeams()
    {
        $users = null;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $teams = $this->entityManager->getRepository(Team::class)->findAll();
        } else {
            $teams = $this->entityManager->getRepository(Team::class)->findBy(['companyId' => $this->getUser()->getCompany()->getId()]);
            $users = $this->entityManager->getRepository(User::class)->findBy(['company' => $this->getUser()->getCompany()]);
        }

        return $this->render('admin/teams/index.html.twig', [
            'teams' => $teams,
            'users' => $users
        ]);
    }

    #[Route('/teams/create', name: 'admin_teams_new')]
    public function newTeam()
    {
        $users = $this->entityManager->getRepository(User::class)->findBy(['company' => $this->getUser()->getCompany()]);

        return $this->render('admin/teams/new.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/teams/new', name: 'admin_teams_create')]
    public function createTeam(Request $request)
    {

        $team = new Team();
        $team->setName($request->get('name'));
        $team->setDescription($request->get('description'));
        $team->setCompanyId($this->getUser()->getCompany()->getId());

        $entityManager = $this->entityManager;
        $users = $entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('user', 'user.email')
            ->where('user.company = ' . $this->getUser()->getCompany()->getId())
            ->getQuery()
            ->getResult()
        ;

        if (isset($users[$request->get('lead')])) {
            $leader = $users[$request->get('lead')];

            $leader->addRole('ROLE_TEAM_MANAGER');
            $team->setLead($leader);
        }

        foreach($request->query as $name => $query) {
            if (str_starts_with($name, 'user') && isset($users[$query])) {
                $user = $users[$query];

                $team->addUser($user);
            }
        }

        $entityManager->persist($team);
        $entityManager->persist($leader);

        $entityManager->flush();

        return $this->redirectToRoute('admin_teams');
    }

    #[Route('/team/{id}/user/add', name: 'admin_teams_user_add')]
    public function addUserTeam(Request $request, Team $team)
    {
        $entityManager = $this->entityManager;
        $users = $entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('user', 'user.email')
            ->where('user.company = ' . $this->getUser()->getCompany()->getId())
            ->getQuery()
            ->getResult()
        ;

        foreach($request->query as $name => $query) {
            if (str_starts_with($name, 'user') && isset($users[$query])) {
                $user = $users[$query];

                $team->addUser($user);
                
                $entityManager->persist($user);
            }
        }

        $entityManager->persist($team);

        $entityManager->flush();

        return $this->redirectToRoute('admin_teams');
    }

    #[Route('/team/{id}/user/{user}/remove', name: 'admin_teams_user_remve')]
    public function removeUserFromTeam(Team $team, User $user)
    {
        $entityManager = $this->entityManager;

        $team->removeUser($user);

        $entityManager->persist($team);

        $entityManager->flush();

        $this->addFlash('success', $user->getUsername() . ' removed from ' . $team.getName());

        return $this->redirectToRoute('admin_teams');
    }

    #[Route('/clients', name: 'admin_clients')]
    public function getClients()
    {
        $clients = $this->entityManager->getRepository(Client::class)->findBy(['companyId' => $this->getUser()->getCompany()->getId()]);

        return $this->render('admin/Client/index.html.twig', [
            'clients' => $clients
        ]);
    }

    #[Route('/client/create', name: 'admin_client_create')]
    public function createClient(Request $request)
    {

        $entityManager = $this->entityManager;

        $client = new Client();
        $client->setName($request->get('clientName'));
        $client->setCompanyId($this->getUser()->getCompany()->getId());

        if($request->get('projectName')) {
            $project = $client->getProjects()->current();

            $project->setName($request->get('projectName'));
            $project->setDescription($request->get('projectDescription') ?: "");
            
            $entityManager->persist($client);
        }

        $entityManager->persist($project);

        $entityManager->flush();

        $this->addFlash('success', $client->getName() . ' created.');
        $this->addFlash('success', ($project->getName() ?: 'Untitled') . ' updated.');

        return $this->redirectToRoute('admin_clients');
    }
}