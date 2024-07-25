<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage;

class CompanyUsers
{

    private userRepository $userRepository;

    private UsageTrackingTokenStorage $security;

    public function __construct(UserRepository $userRepository, UsageTrackingTokenStorage $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function getUsers()
    {
        return $this->userRepository->findByCompany($this->security->getToken()->getUser()->getCompany()->getId());
    }

    public function getOtherUsers()
    {
        return$this->userRepository->getOtherUsers($this->security->getToken()->getUser()->getCompany()->getId());
    }
}