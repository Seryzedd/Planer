<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CompanyUsers
{

    private userRepository $userRepository;

    private RequestStack $requestStack;

    public function __construct(UserRepository $userRepository, RequestStack $requestStack)
    {
        $this->userRepository = $userRepository;
        $this->requestStack = $requestStack;
    }

    public function getUsers()
    {
        dump($this->requestStack->getCurrentRequest());
    }
}