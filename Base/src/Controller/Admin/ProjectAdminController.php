<?php

namespace App\Controller\Admin;

use App\Entity\User\User;
use App\Form\HoursSoldType;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProjectRepository;
use App\Repository\ClientRepository;
use App\Form\ProjectType;
use App\Form\Translations\ProjectTranslationsType;
use App\Entity\Translations\ProjectTranslation;

/**
 * admin controller
 */
#[Route('/admin/project')]
class ProjectAdminController extends AdminController
{
    /**
     * @return Response
     */
    #[Route('/list', name: 'admin_projects', defaults: ['admin' => true, 'icon' => 'list-check', 'role' => 'ROLE_ADMIN', 'title' => 'Projects'])]
    public function projects(Request $request, ProjectRepository $projectRepository, ClientRepository $clientRepository): Response
    {
        $entityManager = $this->entityManager;

        $form = null;

        if ($this->getUser()->getCompany()) {
            $newProject = new Project();

            $form = $this->createForm(ProjectType::class, $newProject);

            $form
                ->add('create', SubmitType::class, [
                    'label' => 'create',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;

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
            'form' => $form ? $form : null
        ]);
    }

    #[Route('/{id}', name: 'admin_project_view')]
    public function projectViewAction(Request $request, Project $project)
    {

        $clients = $this->entityManager->getRepository(Client::class)->findBy(['companyId' => $this->getUser()->getCompany()->getId()]);

        $form = $this->createForm(ProjectType::class, $project);

        $form
            ->add('hoursSold', CollectionType::class, [
                'entry_type' => HoursSoldType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

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

    #[Route('/{id}/translate', name: 'admin_project_translate')]
    public function translate(Project $project, array $locales, Request $request)
    {
        if (count($project->getTranslations()) === 0) {
            foreach($locales as $locale) {
                $newTranslation = new ProjectTranslation($locale);

                $project->addTranslation($newTranslation);
            }
        }
        
        $form = $this->createForm(ProjectTranslationsType::class, $project);

        $form
            ->add('Update', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn btn-primary mx-auto'
                ]
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;

            foreach ($project->getTranslations() as $translation) {
                $entityManager->persist($translation);
            }

            $entityManager->persist($project);
            $entityManager->flush();
            
            $this->addFlash('success', 'Project translated.');
        }
        
        return $this->render('admin/Projects/translate.html.twig', [
            'project' => $project,
            'form' => $form
        ]);
    }
}