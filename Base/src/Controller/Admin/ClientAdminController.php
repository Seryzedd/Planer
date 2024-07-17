<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client\Client;
use App\Controller\AdminController;
use App\Repository\ClientRepository;
use App\Form\ClientFormType;

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

            $form = $this->createForm(ClientFormType::class, $newClient);

            $form
                ->add('submit', SubmitType::class, [
                    'label' => 'New',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;

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
                    'Client created'
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

        $form = $this->createForm(ClientFormType::class, $client);

        $form
            ->add('submit', SubmitType::class, [
                'label' => 'New',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

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