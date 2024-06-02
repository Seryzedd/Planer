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

/**
 * @package  App\Controller
 */
class UserController extends BaseController
{

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
     */
    #[Route('/register', name: 'register')]
    #[Route('/register/invitation/{id}', name: 'register_invitation')]
    public function registerAction(Request $request, UserPasswordHasherInterface $encoder, Security $security, ?Invitation $id = null)
    {
        $user = new User();

        $entityManager = $this->entityManager;

        $user->addSchedule(new Schedule($user));

        if ($id && $id->isValid()) {
            $user->setEmail($id->getEmail());
            $user->setCompany($id->getCompany());
        } else {
            $user->addRole('ROLE_COMPANY_LEADER');
            $user->addRole('ROLE_ADMIN');
        }

        $formBuilder = $this->createFormBuilder($user, [
            'attr' => [
                'class' => 'w-50 mx-auto'
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
            ->add('firstName', TextType::class, [
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
            ->add('lastName', TextType::class, [
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
        ;

        if (!$id) {
            $formBuilder
                ->add('email', EmailType::class, [
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
            ;
        }
            
        $formBuilder
            ->add('job', ChoiceType::class, [
                'choices' => USER::JOBS,
                'attr' => [
                    'class' => 'form-control'
                    ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'row_attr' => [
                    "class" => "form-group"
                ],
                'attr' => [
                    'class' => 'w-100 form-control'
                ],
                'label_attr' => [
                    'class' => 'w-100'
                ],
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'w-100 form-control'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirm password',
                    'attr' => [
                        'class' => 'w-100 form-control'
                    ]

                ],
            ])
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

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $form->getData();

            $encoded = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $entityManager->persist($user);

            $entityManager->persist($user->getSchedule()->current());

            $entityManager->flush();

            $this->addFlash('success', $user->getUsername() . ' created !');

            return $this->redirectToRoute('my_schedule', ['id' => $user->getId()]);
        }

        return $this->render('User/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/update', name: 'user_update')]
    public function updateUserInformations(Request $request)
    {
        $user = $this->getUser();

        $formBuilder = $this->createFormBuilder($user, [
            'attr' => [
                'class' => 'mx-auto'
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
            ->add('firstName', TextType::class, [
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
            ->add('lastName', TextType::class, [
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
            ->add('email', EmailType::class, [
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
            ->add('job', ChoiceType::class, [
                'choices' => USER::JOBS,
                'attr' => [
                    'class' => 'form-control'
                    ]
            ])
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

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $form->getData();

            $entityManager = $this->entityManager;

            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', 'User ' . $user->getUsername() . ' updated !');

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

            $this->desactivePastSchedules($id);

            $this->addFlash('success', 'Schedule updated.');

            if ($id->getCompany())
            {
                $security->login($id);
            
                $this->addFlash('info', 'You a logged as ' . $id->getUsername() . '.');

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
        ])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            
            $entityManager->persist($form->getData());

            $entityManager->flush();

            $this->desactivePastSchedules($this->getUser());

            $this->addFlash('success', 'Schedule updated.');

            return $this->redirectToRoute('my_account');
        }

        return $this->render('User/Account/schedule.html.twig', [
            'form' => $form,
            'user' => $this->getUser()
        ]);
    }

    private function desactivePastSchedules(User $user)
    {
        $entityManager = $this->entityManager;
        foreach($user->getSchedule() as $schedule) {
            if($user->getMostRecentSchedule()->getId() !== $schedule->getId()) {
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

        $company = new Company();
        $user->setCompany($company);
        $form = $this->createFormBuilder($company, [
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

            $user->addRole('ROLE_TEAM_MANAGER');
            $user->addRole('ROLE_ADMIN');
            $user->addRole('ROLE_COMPANY_LEADER');

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