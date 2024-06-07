<?php

namespace App\Controller\Admin;

use App\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\AdminController;
use App\Entity\Client\Client;
use App\Entity\Client\Project;
use App\Entity\Work\Assignation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProjectRepository;
use App\Repository\ClientRepository;

/**
 * admin controller
 */
#[Route('/admin/project')]
class ProjectAdminController extends AdminController
{
    /**
     * @return Response
     */
    #[Route('/list', name: 'admin_projects')]
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
            'all' => $projectRepository->findAll(),
            'form' => $form ? $form->createView() : null
        ]);
    }

    #[Route('/{id}', name: 'admin_project_view')]
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
                'empty_data' => null,
                'attr' => ['class' => '']
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'empty_data' => ''
            ])
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

    #[Route('/assignation/{id}', name: 'admin_assignation_update')]
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
}