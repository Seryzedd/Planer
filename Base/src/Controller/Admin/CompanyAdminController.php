<?php

namespace App\Controller\Admin;

use App\Repository\CompanyRepository;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Company;
use App\Entity\CalendarConfigurations;
use App\Form\CompanyOptionsType;


/**
 * admin controller
 */
#[Route('/admin/company')]
class CompanyAdminController extends BaseController
{
    /**
     * @var CompanyRepository $repository
     * @var Request $request
     * 
     * @return Response
     */
    #[Route('/', name: 'admin_company_index')]
    public function index(CompanyRepository $repository, Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Authenticate to create schedule.');

            return $this->redirectToRoute('app_index');
        }

        
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            
            $entityManager->persist($form->getData());

            $entityManager->flush();

            $this->desactivePastSchedules($this->getUser());

            $this->addFlash('success', 'Schedule updated.');

            return $this->redirectToRoute('my_account');
        }
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            
            $entityManager->persist($form->getData());

            $entityManager->flush();

            $this->desactivePastSchedules($this->getUser());

            $this->addFlash('success', 'Schedule updated.');

            return $this->redirectToRoute('my_account');
        }

        return $this->render('admin/Company/Index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @var CompanyRepository $repository
     * 
     * @return Response
     */
    #[Route('/list', name: 'admin_company_list')]
    public function companyList(CompanyRepository $repository)
    {
        $companies = $repository->findAll();

        return $this->render('admin/Company/list.html.twig', [
            'companies' => $companies
        ]);
    }

    /**
     * @var CompanyRepository $repository
     * 
     * @return Response
     */
    #[Route('/options', name: 'admin_company_options')]
    public function companyOptions(Request $request)
    {
        $form = null;

        if ($this->getUser()->getCompany()) {
            if ($this->getUser()->getCompany()->getCalendarConfigurations()) {
                $config = $this->getUser()->getCompany()->getCalendarConfigurations();
            } else {
                $config = new CalendarConfigurations();
                $config->setCompany($this->getUser()->getCompany());
            }

            $form = $this->createForm(CompanyOptionsType::class, $config);

            $form
                ->add('submit', SubmitType::class, [
                    'label' => 'Update',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->entityManager;
                
                $entityManager->persist($form->getData());

                $entityManager->flush();

                $this->desactivePastSchedules($this->getUser());

                $this->addFlash('success', 'Configuration updated.');
            }
        }

        return $this->render('admin/Company/options.html.twig', [
            'form' => $form
        ]);
    }
}