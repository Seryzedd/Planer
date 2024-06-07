<?php

namespace App\Controller\Admin;

use App\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;

/**
 * admin controller
 */
#[Route('/admin/user')]
class UsersAdminController extends BaseController
{
    /**
     * @return Response
     */
    #[Route('/', name: 'admin_users')]
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
     * @var User $user
     * 
     * @return Response
     */
    #[Route('/{id}/view', name: 'admin_user_view')]
    public function userViewAction(User $user): Response
    {
        return $this->render('admin/users/view.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/{id}/role/add/{role}', name: 'admin_user_add_role')]
    public function userAddRole(User $user, string $role): Response
    {

        if (!in_array($role, User::ROLE_LIST)) {
            $this->addFlash('danger', 'This role does not exist.');
        } else {
            $user->addRole($role);

            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Role added on user.');
        }
        
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/{id}/role/remove/{role}', name: 'admin_user_remove_role')]
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
}