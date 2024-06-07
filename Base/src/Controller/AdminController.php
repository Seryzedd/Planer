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

    #[Route('/user/{id}/view', name: 'admin_user_view')]
    public function userViewAction(User $user)
    {
        return $this->render('admin/users/view.html.twig', [
            'user' => $user,
        ]);
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

            $this->addFlash('success', "Role removed to user.");
        }

        return $this->redirectToRoute('admin_users');
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
            $this->addFlash('danger', 'You can\'t create new invitation without company.');
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