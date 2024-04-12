<?php

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class AfternoonSchedule extends AbstractEntity
{

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $startHour = 14;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $startMinutes = 0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $endHour = 18;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $endMinutes = 0;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $working = true;

    /**
     * @var Day
     */
    #[ORM\OneToOne(mappedBy: "afternoon", targetEntity: Day::class, cascade: ['persist', 'remove'])]
    private Day $day;

    /**
     * @param Day $day
     */
    public function __construct(Day $day)
    {
        $this->day = $day;
    }

    /**
     * @return int
     */
    public function getStartHour(): int
    {
        return $this->startHour;
    }

    /**
     * @param int $startHour
     * @return void
     */
    public function setStartHour(int $startHour): void
    {
        $this->startHour = $startHour;
    }

    /**
     * @return int
     */
    public function getStartMinutes(): int
    {
        return $this->startMinutes;
    }

    /**
     * @param int $startMinutes
     * @return void
     */
    public function setStartMinutes(int $startMinutes): void
    {
        $this->startMinutes = $startMinutes;
    }

    /**
     * @return int
     */
    public function getEndHour(): int
    {
        return $this->endHour;
    }

    /**
     * @param int $endHour
     * @return void
     */
    public function setEndHour(int $endHour): void
    {
        $this->endHour = $endHour;
    }

    /**
     * @return int
     */
    public function getEndMinutes(): int
    {
        return $this->endMinutes;
    }

    /**
     * @param int $endMinutes
     * @return void
     */
    public function setEndMinutes(int $endMinutes): void
    {
        $this->endMinutes = $endMinutes;
    }

    /**
     * @return bool
     */
    public function isWorking(): bool
    {
        return $this->working;
    }

    /**
     * @param bool $working
     * @return void
     */
    public function setWorking(bool $working): void
    {
        $this->working = $working;
    }
}