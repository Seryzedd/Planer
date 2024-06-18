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

    #[Route('/', name: 'admin_invitations')]
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
                    ->andWhere($querybuilder->expr()->eq('p.email', ':email'))
                    ->setparameter('from', new \DateTime('-1 day'))
                    ->setparameter('email', $newInvitation->getEmail())
                    ->getquery();

                dump($query->getResult());
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
}