<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;
use App\Entity\User\Absence;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AbsenceType;

/**
 * admin controller
 */
#[Route('/account')]
class AccountController extends BaseController
{

    #[Route('/', name: 'my_account')]
    public function account()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->render('User/Account/index.html.twig');
    }

    #[Route('/absence/create', name: 'new_absence')]
    public function absencesAction(Request $request)
    {
        $absence = new Absence();

        $absence->setUser($this->getUser());

        $form = $this->createForm(AbsenceType::class, $absence);

        $form
            ->add('create', SubmitType::class, [
                'label' => 'create',
                'row_attr' => [
                    'class' => 'text-center mt-3'
                ],
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->entityManager;

            $entityManager->persist($absence);
            $entityManager->persist($absence->getUser());
            $entityManager->flush();
            
            $this->addFlash('success', 'Absence created.');
        }

        return $this->render('User/Account/Absence.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/absence/{id}/update', name: 'absence_update')]
    public function updateAbsenceAction(Request $request, Absence $absence)
    {
        $formBuilder = $this->createFormBuilder($absence, []);

        $formBuilder
            ->add('type', ChoiceType::class, [
                'choices' => Absence::ABSENCE_TYPE_LIST,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('from', TextType::class, [
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('to', TextType::class, [
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $formBuilder
        ->get('from')
        ->addModelTransformer(new CallbackTransformer(
            function (?\DateTime $date): string {
                if (!$date) {
                    $date = new \DateTime();
                }
                
                return $date->format('d/m/Y');
            },
            function (?string $tagsAsDateTime): \DateTime {
                
                return \DateTime::createFromFormat('d/m/Y', $tagsAsDateTime);
            }
        ));

        $formBuilder
        ->get('to')
        ->addModelTransformer(new CallbackTransformer(
            function (?\DateTime $date): string {
                if (!$date) {
                    $date = new \DateTime();
                }
                
                return $date->format('d/m/Y');
            },
            function (?string $tagsAsDateTime): \DateTime {
                return \DateTime::createFromFormat('d/m/Y', $tagsAsDateTime);
            }
        ));

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->entityManager;

            $entityManager->persist($absence);
            $entityManager->persist($absence->getUser());
            $entityManager->flush();
            
            $this->addFlash('success', 'Absence updated.');
        }

        return $this->render('User/Account/Absence.html.twig', [
            'form' => $form->createView()
        ]);
    }
}