<?php

namespace App\Controller\Admin;

use App\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Entity\User\Team;
use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\TeamType;

/**
 * Team admin controller
 */
#[Route('/admin/team')]
class TeamAdminController extends AdminController
{
    #[Route('/', name: 'admin_teams', defaults: ['myTeam' => false])]
    #[Route('/MyTeam', name: 'admin_my_team', defaults: ['myTeam' => true])]
    public function getTeams(TeamRepository $teamRepository, bool $myTeam)
    {
        $users = null;

        if ($myTeam) {
            $teams = $teamRepository->findTeamByUser($this->getUser());
        } else {
            if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                $teams = $teamRepository->findAll();
            } else {
                $teams = $teamRepository->findByCompany($this->getUser()->getCompany()->getId());
                $users = $this->entityManager->getRepository(User::class)->findBy(['company' => $this->getUser()->getCompany()]);
            }
        }   

        return $this->render('admin/teams/index.html.twig', [
            'teams' => $teams,
            'users' => $users
        ]);
    }

    #[Route('/create', name: 'admin_teams_new')]
    public function newTeam()
    {
        $form = $this->createForm(TeamType::class, new Team());

        $form
            ->add('update', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $users = $this->entityManager->getRepository(User::class)->findBy(['company' => $this->getUser()->getCompany()]);

        return $this->render('admin/teams/new.html.twig', [
            'users' => $users,
            'form' => $form
        ]);
    }

    #[Route('/new', name: 'admin_teams_create')]
    public function createTeam(Request $request)
    {

        dump($request); die ;
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

    #[Route('/{id}/user/{user}/remove', name: 'admin_teams_user_remve')]
    public function removeUserFromTeam(Team $team, User $user)
    {
        $entityManager = $this->entityManager;

        $team->removeUser($user);

        $entityManager->persist($team);

        $entityManager->flush();

        $this->addFlash('success', $user->getUsername() . ' removed from ' . $team.getName());

        return $this->redirectToRoute('admin_teams');
    }

    #[Route('/{id}/user/add', name: 'admin_teams_user_add')]
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

    #[Route('/team/{id}/view', name: 'admin_team_view')]
    public function teamViewAction(Request $request, Team $team, UserRepository $userRepository)
    {
        $form = $this->createForm(TeamType::class, $team);

        $form
            ->add('update', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;

            $entityManager->persist($team);
            $entityManager->flush();
            
            $this->addFlash(
                'success',
                'Team updated.'
             );
            
        }

        return $this->render('admin/teams/view.html.twig', [
            'team' => $team,
            'form' => $form->createView()
        ]);
    }
}