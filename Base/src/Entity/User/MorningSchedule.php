<?php

namespace App\Entity\User;

use App\Entity\User\AbstractDayPart;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User\Day;

#[ORM\Entity()]
class MorningSchedule extends AbstractDayPart
{
    /**
     * @var Day
     */
    #[ORM\OneToOne(mappedBy: "morning", targetEntity: Day::class, cascade: ['persist', 'remove'])]
    private Day $day;

    /**
     * @param Day $day
     */
    public function __construct(Day $day)
    {
        $this->setStartHour(8);
        $this->setEndHour(12);
        $this->day = $day;
    }

    public function getDay(): Day
    {
        return $this->day;
    }

    public function setDay(): self
    {
        $this->day;

        return $this;
    }
}