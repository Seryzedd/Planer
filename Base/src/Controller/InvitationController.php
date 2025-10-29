<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Invitation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class InvitationController extends BaseController
{
    #[Route('/invitation/{id}', name: 'app_invitation_validate')]
    public function index(Request $request, Invitation $invitation): Response
    {
        if ($this->getUser()) {
            $this->addFlash(
               'danger',
               'You are already logged in.'
            );

            return $this->redirectToRoute('my_account');
        }

        if ($invitation->getStatus() !== Invitation::VALID_STATUS) {
            $this->addFlash(
               'danger',
               'Invalid Invitation.'
            );

            return $this->redirectToRoute('app_index');
        }

        $formBuilder = $this->createFormBuilder(null, [
            'attr' => [
                'class' => 'col-12 col-lg-8'
            ]
        ]);

        $formBuilder
            ->add('code', TextType::class, [
                'attr' => [
                    'class' => 'w-50 mx-auto text-center'
                ]
            ])
            ->add('Validate', SubmitType::class, [
                'label' => 'Validate'
            ])
        ;

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            
            if ($form->getData()['code'] === $invitation->getCode()) {
                $this->addFlash('success', 'Complete your account.');

                $invitation->setStatus(Invitation::USED_STATUS);

                $entityManager->persist($invitation);
                $entityManager->flush();
                
                return $this->redirectToRoute('register_invitation', ['id' =>  $invitation->getId()]);
            } else {
                $this->addFlash('danger', 'Invalid code.');
            }
        }

        return $this->render('invitation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
