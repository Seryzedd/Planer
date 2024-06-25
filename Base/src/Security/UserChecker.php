<?php

namespace App\Security;

use App\Entity\User\User as AppUser;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserChecker implements UserCheckerInterface
{
    public function __construct(private RequestStack $requestStack, private UrlGeneratorInterface $router) {}

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->isVerified()) {
            $session = $this->requestStack->getSession();

            $session->getFlashBag()->add(
                    'danger', "User hasn\'t been verified with email verification link. This email is sent on user creation.<br> <a href=" . $this->router->generate('user_email_request') . ">I don\'t received email</a>."
                )
            ;
            throw new AuthenticationException('User not verified.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // user account is not verified with email
        if (!$user->isVerified()) {
            $session->getFlashBag()->add(
                'danger', "User hasn\'t been verified with email verification link. This email is sent on user creation.<br> <a href=" . $this->router->generate('user_email_check', ['id' => $user->getId()]) . ">I don\'t received email</a>."
            )
        ;

            throw new AccountExpiredException('User not verified.');
        }
    }
}