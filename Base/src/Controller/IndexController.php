<?php

namespace App\Controller;

use App\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('index/index.html.twig', []);
    }

    #[Route('/homepage', name: 'app_home')]
    public function mainIndex(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        return $this->render('index/main.html.twig', []);
    }
}
