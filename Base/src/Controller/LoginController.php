<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\LoginType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends BaseController
{

    private $authUtils;

    public function __construct(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        parent::__construct($entityManager, $translator);
        $this->authUtils = $authenticationUtils;
    }

    /**
     *
     */
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        $form = $this->createForm(LoginType::class, null);

        $form
            ->add('login', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;

        $lastUsername = '';
        
        if ($form->isSubmitted() && $form->isValid()) {
            dump($request); die ;
            // get the login error if there is one
            // $error = $authenticationUtils->getLastAuthenticationError();
            $error = $authenticationUtils->getLastAuthenticationError();

            if ($error) {
                $this->addFlash('danger', 'Connexion error ! Check your Username and password and try again.');
            }

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();
        }
        
        return $this->render('login/index.html.twig', [
            'form' => $form,
            'lastUserName' => $lastUsername
        ]);
    }
}
