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
use Symfony\Bundle\SecurityBundle\Security;

use App\Entity\User\User;

class LoginController extends BaseController
{

    /**
     * @var AuthenticationUtils $authenticationUtils
     * 
     */
    #[Route('/connect', name: 'connect')]
    public function connectUser(AuthenticationUtils $authenticationUtils)
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $this->addFlash("danger", 'Login error !');

            return $this->redirectToRoute('login');
        }

        $this->addFlash("success", 'Login succeed !');

        return $this->redirectToRoute('my_account');
    }

    /**
     * @var Security $security
     * @var Request $request
     * 
     * @return Response
     */
    #[Route('/login', name: 'login')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LoginType::class, new User(), ['action' => $this->generateUrl('connect')]);

            $form
                ->add('login', SubmitType::class, [
                    'label' => 'Login',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
            ;

        return $this->render('login/index.html.twig', [
            'form' => $form
        ]);
    }
}
