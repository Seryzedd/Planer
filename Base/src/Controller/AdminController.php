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

    #[Route('/user/{id}/view', name: 'admin_user_view')]
    public function userViewAction(User $user)
    {
        return $this->render('admin/users/view.html.twig', [
            'user' => $user,
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
    public function projects(Request $request, ProjectRepository $projectRepository, ClientRepository $clientRepository): Response
    {
        $entityManager = $this->entityManager;

        $form = null;

        if ($this->getUser()->getCompany()) {
            $newProject = new Project();

            $formBuilder = $this->createFormBuilder($newProject, [
                'attr' => [
                    'class' => 'text-center'
                ]
            ]);

            $formBuilder
                ->add('name', TextType::class, [])
                ->add('client', EntityType::class, [
                    'class' => Client::class,
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'choices' => $this->getUser()->getCompany() ? $clientRepository->findByCompany($this->getUser()->getCompany()->getId()) : $clientRepository->findAll(),
                    'choice_label' => 'name'
                ])
                ->add('deadline', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['class' => '']
                ])
                ->add('description', TextareaType::class, [
                    'required' => false,
                    "empty_data" => ''
                ])
                ->add('create', SubmitType::class, [
                    'label' => 'create',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;

            $form = $formBuilder->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->entityManager;

                $entityManager->persist($newProject);
                $entityManager->flush();
                
                $this->addFlash('success', 'Project created.');
            }
        }
        
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $projects = $projectRepository->findAll();
        } else {
            $projects = $projectRepository->findByCompany($this->getUser()->getCompany()->getId());
        }

        return $this->render('admin/Projects/index.html.twig', [
            'projects' => $projects,
            'form' => $form ? $form->createView() : null
        ]);
    }

    #[Route('/project/{id}', name: 'admin_project_view')]
    public function projectViewAction(Request $request, Project $project)
    {

        $clients = $this->entityManager->getRepository(Client::class)->findBy(['companyId' => $this->getUser()->getCompany()->getId()]);

        $formBuilder = $this->createFormBuilder($project, []);

        $formBuilder
            ->add('name', TextType::class, [])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => $clients,
                'choice_label' => 'name'
            ])
            ->add('deadline', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
                'attr' => ['class' => '']
            ])
            ->add('description', TextareaType::class, [])
            ->add('submit', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->entityManager;
            $entityManager->persist($project);
            $entityManager->flush();
            
            $this->addFlash('success', 'Project updated.');
        }

        return $this->render('admin/Projects/view.html.twig', [
            'form' => $form
        ]);
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
                       'Invitation created'
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

        $this->addFlash('success', $this->translator->trans('Team %name% created.', ['name' => $team->getName()]));

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
    public function getClients(Request $request, ClientRepository $clientRepository)
    {
        
        $entityManager = $this->entityManager;

        $form = null;

        if ($this->getUser()->getCompany()) {
            
            $newClient = new Client();

            $newClient->setCompanyId($this->getUser()->getCompany()->getId());

            $formBuilder = $this->createFormBuilder($newClient, [
                'attr' => [
                    'class' => 'text-center'
                ]
            ]);

            $formBuilder
                ->add('name', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Client name'
                    ]
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'New',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;

            $form = $formBuilder->getForm();

            $form->handleRequest($request);
        } else {
            $this->addFlash('info', 'You can\'t create new invitation without company.');
        }

        if ($form) {
            
            if ($form->isSubmitted() && $form->isValid()) {
    
                $entityManager->persist($newClient);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Invitation create'
                    );
            }
        }

        $clients = $clientRepository->findByCompany($this->getUser()->getCompany()->getId());

        return $this->render('admin/Client/index.html.twig', [
            'clients' => $clients,
            'form' => $form ? $form->createView() : $form
        ]);
    }

    #[Route('/client/{id}', name: 'admin_client_view')]
    public function clientViewAction(Request $request, Client $client)
    {
        $formBuilder = $this->createFormBuilder($client, []);

        $formBuilder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'text-center'
                ],
                'label_attr' => [
                    'class' => 'text-center w-100'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            
            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client updated.');
        }

        return $this->render('admin/Client/view.html.twig', [
            'client' => $client,
            'form' => $form
        ]);
    }
}