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

/**
 * admin controller
 */
#[Route('/admin/company')]
class CompanyAdminController extends BaseController
{
    /**
     * 
     */
    #[Route('/', name: 'admin_company_index')]
    public function index(CompanyRepository $repository, Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Authenticate to create schedule.');

            return $this->redirectToRoute('app_index');
        }

        $form = $this->createFormBuilder($this->getUser()->getCompany(), [])
        ->add('name', TextType::class, [
            'attr' => [
                'class' => 'text-center'
            ]
        ])
        ->add('country', ChoiceType::class, [
            'choices' => array_flip(Company::COUNTRY_LIST),
            'attr' => [
                'class' => 'form-control text-center'
            ]
        ])
        ->add('Validate', SubmitType::class, [
        ])
        ->getForm();

        $form->handleRequest($request);

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
}