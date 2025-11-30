<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UserCostType;
use App\Entity\User\User;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;

#[Route('/admin/cost')]
final class CostController extends BaseController
{
    #[Route('/{id}', name: 'app_cost')]
    public function index(User $user, Request $request): Response
    {
        $form = $this->createFormBuilder($user)
            ->add('costs', CollectionType::class, [
                'entry_type' => UserCostType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Validate',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->entityManager;

            foreach($user->getCosts() as $cost) {
                $cost->setUser($user);
                $entityManager->persist($cost);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Costs updated.');
        }
        return $this->render('admin/Cost/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
