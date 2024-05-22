<?php

namespace App\Entity\User\Security;

use App\Entity\AbstractEntity;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use App\Repository\PasswordResettingRepository;
use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordResettingRepository::class)]
class PasswordResetting extends AbstractEntity implements ResetPasswordRequestInterface
{
    /**
     * @var User
     */
    #[ORM\OneToOne(mappedBy: 'PasswordResetting', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private User $user;

    use \SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

    /**
     * Get the user whom requested a password reset.
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}