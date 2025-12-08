<?php

namespace App\Controller;

use App\Entity\User\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;

class IndexController extends BaseController
{
    #[Route('/', name: 'app_index')]
    public function index(UserRepository $userRep): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        
        
        return $this->render('index/index.html.twig', []);
    }

    #[Route('/homepage', name: 'app_home')]
    public function mainIndex(ClientRepository $clientRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        if ($this->getUser()->getCompany()) {
            $clients = $clientRepository->findByCompany($this->getUser()->getCompany()->getId());
        } else {
            $clients = $clientRepository->findAll();
        }

        return $this->render('index/main.html.twig', [
            'clients' => $clients
        ]);
    }
}
