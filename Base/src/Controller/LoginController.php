<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends BaseController
{

    private $authUtils;

    public function __construct(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->authUtils = $authenticationUtils;
    }

    /**
     *
     */
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
         // $error = $authenticationUtils->getLastAuthenticationError();
        $error = $authenticationUtils->getLastAuthenticationError();

         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'errors' => $error,
            'lastUserName' => $lastUsername
        ]);
    }
}
