<?php

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User\User as targetUser;

#[ORM\Entity()]
class Schedule extends AbstractEntity
{

    /**
     * @var User
     */
    #[ORM\OneToOne(mappedBy: 'schedule', targetEntity: targetUser::class, cascade: ['persist', 'remove'])]
    private User $user;

    const WEEK_DAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    ];

    /**
     * @var User
     */
    #[ORM\OneToMany(mappedBy: "schedule", targetEntity: Day::class, cascade: ['persist', 'remove'])]
    private Collection $days;

    /**
     * @return void
     */
    public function __construct(User $user)
    {
        $days = new ArrayCollection();

        $this->user = $user;

        $i = 1;
        foreach ($this::WEEK_DAYS as $dayName) {
            $day = new Day($dayName, $i, $this);
            $days->add($day);

            $i++;
        }

        $this->days = $days;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return void
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Collection
     */
    public function getDays(): Collection
    {
        return $this->days;
    }

    /**
     * @param Collection $days
     * @return void
     */
    public function setDays(Collection $days): void
    {
        $this->days = $days;
    }
}