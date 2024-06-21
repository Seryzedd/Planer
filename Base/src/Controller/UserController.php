<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use App\Entity\User\Day;
use App\Form\DayFormType;
use App\Form\ScheduleType;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\User\User;
use App\Entity\Invitation;
use App\Entity\Company;
use App\Entity\User\Schedule;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\UserInformationsType;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\FileUploader;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package  App\Controller
 */
class UserController extends BaseController
{
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator, FileUploader $fileUploader)
    {
        parent::__construct($entityManager, $translator);
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/login/test", name="")
     */
    public function requestLoginLink(UserRepository $userRepository, Request $request)
    {

        $formBuilder = $this->createFormBuilder(null, [
            'attr' => [
                'class' => 'w-50'
            ]
        ]);

        $formBuilder
            ->add('username', TextType::class, [
                'row_attr' => [
                    "class" => "form-group"
                ],
                'attr' => [
                    'class' => 'w-100 form-control'
                ],
                'label_attr' => [
                    'class' => 'w-100'
                ]
            ])
            ->add('password', PasswordType::class, [
                'row_attr' => [
                    "class" => "form-group"
                ],
                'attr' => [
                    'class' => 'w-100 form-control'
                ],
                'label_attr' => [
                    'class' => 'w-100'
                ]
            ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        // check if login form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // load the user in some way (e.g. using the form input)
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            // create a login link for $user this returns an instance
            // of LoginLinkDetails

            // ... send the link and return a response (see next section)
        }

        // if it's not submitted, render the "login" form
        return $this->render('User/login.html.twig', [
            'form' => $form->createView()
        ]);
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
            $user->setHeadshot($fileName);
        }
    }

    #[Route('/update', name: 'user_update')]
    public function updateUserInformations(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserInformationsType::class, $user, ['attr' => ['class' => 'row align-items-center']]);
            
        $form
            ->add('Validate', SubmitType::class, [
                'row_attr' => [
                    "class" => "form-group d-flex justify-content-center mt-2"
                ],
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'Validate'
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $form->getData();

            $entityManager = $this->entityManager;

            /** @var UploadedFile $brochureFile */
            $file = $form->get('headshot')->getData();

            $this->updateHeadshot($file, $user);

            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', 'User updated.');

            return $this->redirectToRoute('my_account');
        }

        return $this->render('User/account/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/account/{id}/schedule', name: 'my_schedule')]
    public function mySchedule(User $id, Request $request, Security $security)
    {

        $form = $this->createFormBuilder($id->getMostRecentSchedule(), [
            'attr' => [
                'class' => 'mx-auto'
            ]
        ])
        ->add('startAt', DateType::class, [
            // renders it as a single text box
            'widget' => 'single_text',
        ])
        ->add('days', CollectionType::class, [
            'entry_type' => ScheduleType::class,
            'entry_options' => [
                'data_class' => Day::class,
                'label' => false
            ],
            'label' => false
        ])
        ->add('Validate', SubmitType::class, [
        ])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            
            $entityManager->persist($form->getData());

            $entityManager->flush();

            $this->desactivePastSchedules($id, $form->getData());

            $this->addFlash('success', 'Schedule updated.');

            if ($id->getCompany())
            {
                $security->login($id);
            
                $this->addFlash('info', 'You are logged in.');

                return $this->redirectToRoute('my_account');
            }

            $company = new Company();
            $id->setCompany($company);
            $company->setCountry('FR');

            $entityManager->persist($id->getCompany());

            $entityManager->flush();

            $security->login($id);
            
            $this->addFlash('info', 'You a logged as ' . $user->getUsername() . '.');

            return $this->redirectToRoute('my_company');
        }

        return $this->render('User/Account/schedule.html.twig', [
            'form' => $form,
            'user' => $id
        ]);
    }

    #[Route('/account/schedule/new', name: 'new_schedule')]
    public function createNewSchedule(Request $request)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Authenticate to create schedule.');

            return $this->redirectToRoute('app_index');
        }

        $form = $this->createFormBuilder(new Schedule($this->getUser()), [
            'attr' => [
                'class' => 'mx-auto'
            ]
        ])
        ->add('startAt', DateType::class, [
            // renders it as a single text box
            'widget' => 'single_text',
        ])
        ->add('days', CollectionType::class, [
            'entry_type' => ScheduleType::class,
            'entry_options' => [
                'data_class' => Day::class,
                'label' => false
            ],
            'label' => false
        ])
        ->add('Validate', SubmitType::class, [
            'attr' => [
                'class' => 'mx-auto btn btn-primary'
            ]
        ])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;

            $schedule = $form->getData();

            $alreadyExistSchedule = $entityManager->getRepository(Schedule::class)->findOneBy(['startAt' => $schedule->getStartAt()]);

            if ($alreadyExistSchedule) {
                $alreadyExistSchedule->setDays($schedule->getDays());
                $alreadyExistSchedule->setActive(true);

                $schedule = $alreadyExistSchedule;
            }
            
            $entityManager->persist($schedule);

            $entityManager->flush();

            $this->desactivePastSchedules($this->getUser(), $schedule);

            $this->addFlash('success', 'Schedule updated.');

            return $this->redirectToRoute('my_account');
        }

        return $this->render('User/Account/schedule.html.twig', [
            'form' => $form,
            'user' => $this->getUser()
        ]);
    }

    private function desactivePastSchedules(User $user, Schedule $schedule)
    {
        $entityManager = $this->entityManager;
        foreach($user->getSchedule() as $item) {
            if($item !== $schedule->getId()) {
                $schedule->setActive(false);

                $entityManager->persist($schedule);
            }
        }

        $entityManager->flush();
    }

    #[Route('/account/company', name: 'my_company')]
    public function myCompany(Request $request)
    {
        $user = $this->getUser();

        if (!$user->getCompany()) {
            $company = new Company();
            $user->setCompany($company);
        }
        
        $form = $this->createFormBuilder($user->getCompany(), [
            'attr' => [
                'class' => 'mx-auto'
            ]
        ])
            ->add('name', TextType::class, [])
            ->add('country', ChoiceType::class, [
                'choices' => array_flip(Company::COUNTRY_LIST),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Validate', SubmitType::class, [])
            ->getForm()
            ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;

            $entityManager->persist($user);
            $entityManager->persist($form->getData());

            $entityManager->flush();

            $this->addFlash('success', 'Company ' . $company->getName() . ' created.');

            return $this->redirectToRoute('app_index');
        }

        return $this->render('User/Account/company.html.twig', ['form' => $form]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}