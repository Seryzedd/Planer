<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use App\Entity\User\User;

class UsersIdListExtensionRuntime implements RuntimeExtensionInterface
{
    
    public function getIdList(array $users)
    {
        return array_map(function(User $user){
            return $user->getId();
        }, $users);
    }
}
