<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client\Client;
use App\Controller\AdminController;
use App\Repository\ClientRepository;

/**
 * admin controller
 */
#[Route('/admin/client')]
class ClientAdminController extends AdminController
{
    #[Route('/list', name: 'admin_clients')]
    public function getClients(Request $request, ClientRepository $clientRepository)
    {
        
        $entityManager = $this->entityManager;

        $form = null;

        if ($this->getUser()->getCompany()) {
            
            $newClient = new Client();

            $newClient->setCompanyId($this->getUser()->getCompany()->getId());

            $formBuilder = $this->createFormBuilder($newClient, [
                'attr' => [
                    'class' => 'text-center'
                ]
            ]);

            $formBuilder
                ->add('name', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Client name'
                    ]
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'New',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;

            $form = $formBuilder->getForm();

            $form->handleRequest($request);
        } else {
            $this->addFlash('info', 'You can\'t create new invitation without company.');
        }

        if ($form) {
            
            if ($form->isSubmitted() && $form->isValid()) {
    
                $entityManager->persist($newClient);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Invitation create'
                    );
            }
        }

        $clients = $clientRepository->findByCompany($this->getUser()->getCompany()->getId());

        return $this->render('admin/Client/index.html.twig', [
            'clients' => $clients,
            'form' => $form ? $form->createView() : $form
        ]);
    }

    #[Route('/{id}', name: 'admin_client_view')]
    public function clientViewAction(Request $request, Client $client)
    {
        $formBuilder = $this->createFormBuilder($client, []);

        $formBuilder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'text-center'
                ],
                'label_attr' => [
                    'class' => 'text-center w-100'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            
            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client updated.');
        }

        return $this->render('admin/Client/view.html.twig', [
            'client' => $client,
            'form' => $form
        ]);
    }
}