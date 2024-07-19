<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use App\Entity\Invitation;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    private FileUploader $fileUploader;

    public function __construct(EmailVerifier $emailVerifier, FileUploader $fileUploader)
    {
        $this->emailVerifier = $emailVerifier;
        $this->fileUploader = $fileUploader;
    }

    #[Route('/register', name: 'register')]
    #[Route('/register/invitation/{id}', name: 'register_invitation')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ?Invitation $id = null): Response
    {
        $user = new User();

        if ($id) {
            $user->setEmail($id->getEmail());
            $user->setCompany($id->getCompany());
        }

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'attr' => 
                [
                    'class' => 'row align-items-center p-3'
                ]
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            /** @var UploadedFile $brochureFile */
            $file = $form->get('headshot')->getData();

            if ($file) {
                $this->updateHeadshot($file, $user);
            } else {
                $user->setHeadshot('');
            }

            if ($id && $id->isValid()) {
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('Planer@no-reply.fr', 'Paner'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                $this->addFlash('info', 'Planer sent an email to registered user.<br>Check mail box to validate user, unless connexion won\'t work.');
            } else {
                $user->addRole('ROLE_TEAM_MANAGER');
                $user->addRole('ROLE_ADMIN');
                $user->addRole('ROLE_COMPANY_LEADER');

                $company = new Company();
                $user->setCompany($company);
                $entityManager->persist($company);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            if (!$id && $id->isValid()) {
                // generate a signed url and email it to the user
                
                return $this->redirectToRoute('my_company');
            }

            // do anything else you need here, like send an email

            return $this->redirectToRoute('new_schedule');
        }

        return $this->render('User/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') === true) {
            $this->addFlash('danger', 'To Check user, logout first.');
            return $this->redirectToRoute('login');
        }

        $user = $userRepository->find((int) $request->get('id'));
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('login');
    }

    /**
     * @var UploadedFile $file
     */
    private function updateHeadshot(UploadedFile $file, User $user)
    {
        if ($file) {
                
            try {
                $fileName = $this->fileUploader->upload($file);
            } catch (FileException $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $user->setHeadshot($fileName ?: '');
        }
    }
}
