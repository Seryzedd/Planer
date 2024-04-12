<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;

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
}