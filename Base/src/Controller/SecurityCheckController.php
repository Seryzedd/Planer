<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\UserRepository;

/**
 * 
 */
#[Route('/security')]
class SecurityCheckController extends BaseController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        parent::__construct($entityManager, $translator);
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function check()
    {
        throw new \LogicException('This code should never be reached');
    }

    #[Route('/verify/email/request', name: 'user_email_request')]
    public function checkingUserEmailRequest(Request $request, UserRepository $userRepository)
    {
        $form = $this->createFormBuilder(null, [
                'attr' => [
                    'class' => 'mx-auto'
                ]
            ])
            ->add('identifier', TextType::class, [
                'label' => 'Username / email',
                'attr' => [
                    'placeholder' => 'Username or email',
                    'class' => 'text-center'
                ]
            ])
            ->add('Validate', SubmitType::class, [])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $identifier = $form->get('identifier')->getNormData();

            $user = $userRepository->findByIdentifier($identifier);
            
            $this->addFlash('info', 'If user found, an email will be sent.');

            if ($user) {
                return $this->redirectToRoute('user_email_check', ['username' => $user->getUserName()]);
            }

            return $this->redirectToRoute('login');
        }

        return $this->render('login/accountVerifier.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/{username}/check', name: 'user_email_check')]
    public function resendUserVerification(string $username, UserRepository $userRepository)
    {
        $user = $userRepository->findByIdentifier($username);

        $translator = $this->translator;

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('Planer@no-reply.fr', 'Paner'))
                    ->to($user->getEmail())
                    ->subject($translator->trans('Confirm my Email on Planer'))
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

        $this->addFlash('info', 'Email has been resend.');

        return $this->redirectToRoute('login');
    }
}
