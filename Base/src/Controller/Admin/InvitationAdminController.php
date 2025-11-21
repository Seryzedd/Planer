<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Invitation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\AdminController;
use App\Repository\InvitationRepository;
use App\Service\EmailSender;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * admin controller
 */
#[Route('/admin/invitation')]
class InvitationAdminController extends AdminController
{

    #[Route('/', name: 'admin_invitations', defaults: ['admin' => true, 'icon' => 'envelopes-bulk', 'role' => 'ROLE_ADMIN', 'title' => 'Invitations'])]
    public function invitations(Request $request, InvitationRepository $invitationRepository, EmailSender $emailSender)
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

        if (!$this->getUser()->getCompany()) {
            $this->addFlash('danger', 'You can\'t create new invitation without company.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if ($user->getCompany()) {
                $entityManager = $this->entityManager;

                $querybuilder = $entityManager->getRepository(Invitation::class)->createquerybuilder('p');
                $query = $querybuilder
                    ->where($querybuilder->expr()->gte('p.date', ':from'))
                    ->andWhere($querybuilder->expr()->eq('p.email', ':email'))
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

                    $data = [
                        'url' => $this->generateUrl('app_invitation_validate', ['id' => $newInvitation->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                        'code' => $newInvitation->getCode(),
                    ];

                    $emailSender->sendEmail($newInvitation->getEmail(), $this->translator->trans('Planer Invitation'), 'Email/Invitation.html.twig', $data);

                    $this->addFlash(
                        'info',
                        'Email sent on email Invitation. <br>Check you\'re email mailbox and spam.'
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
            $invitations = $invitationRepository->findByCompany($this->getUser()->getCompany()->getId(), 'DESC');
        }
        
        return $this->render('admin/invitations/index.html.twig', [
            'invitations' => $invitations,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/remove', name: 'admin_remove_invitation')]
    public function remove(Invitation $invitation)
    {
        $entityManager = $this->entityManager;

        $entityManager->remove($invitation);
        $entityManager->flush();

        $this->addFlash('success', 'Invitation deleted');

        return $this->redirectToRoute('admin_invitations');
    }

    #[Route('/{id}/cancel', name: 'admin_cancel_invitation')]
    public function cancel(Invitation $invitation)
    {
        $entityManager = $this->entityManager;

        $invitation->setStatus(Invitation::CANCEL_STATUS);

        $entityManager->persist($invitation);
        $entityManager->flush();

        $this->addFlash('success', 'Invitation canceled');

        return $this->redirectToRoute('admin_invitations');
    }

    #[Route('/{id}/active', name: 'admin_reactive_invitation')]
    public function activate(Invitation $invitation)
    {
        $entityManager = $this->entityManager;

        $invitation->setStatus(Invitation::VALID_STATUS);
        $invitation->setDate(new \DateTime());
        
        $entityManager->persist($invitation);
        $entityManager->flush();

        $this->addFlash('success', 'Invitation activated');

        return $this->redirectToRoute('admin_invitations');
    }
}