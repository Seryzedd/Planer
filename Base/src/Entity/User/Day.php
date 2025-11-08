<?php

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User\MorningSchedule;
use App\Entity\User\AfternoonSchedule;

#[ORM\Entity()]
class Day extends AbstractEntity
{

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private string $name = "";

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $number = 1;

    /**
     * @var MorningSchedule
     */
    #[ORM\OneToOne(inversedBy: "day", targetEntity: MorningSchedule::class, cascade: ['persist', 'remove'])]
    private MorningSchedule $morning;

    /**
     * @var AfternoonSchedule
     */
    #[ORM\OneToOne(inversedBy: "day", targetEntity: AfternoonSchedule::class, cascade: ['persist', 'remove'])]
    private AfternoonSchedule $afternoon;

    /**
     * @var Schedule
     */
    #[ORM\ManyToOne(targetEntity: Schedule::class, inversedBy: 'days', cascade: ['persist', 'remove'])]
    private Schedule $schedule;

    public function __construct(string $name, int $number,Schedule $schedule)
    {
        $this->name = $name;
        $this->number = $number;
        $this->schedule = $schedule;
        $this->afternoon = new AfternoonSchedule($this);
        $this->morning = new MorningSchedule($this);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return void
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return MorningSchedule
     */
    public function getMorning(): MorningSchedule
    {
        return $this->morning;
    }

    /**
     * @param MorningSchedule $morning
     * @return void
     */
    public function setMorning(MorningSchedule $morning): void
    {
        $this->morning = $morning;
    }

    /**
     * @return AfternoonSchedule
     */
    public function getAfternoon(): AfternoonSchedule
    {
        return $this->afternoon;
    }

    /**
     * @param AfternoonSchedule $afternoon
     * @return void
     */
    public function setAfternoon(AfternoonSchedule $afternoon): void
    {
        $this->afternoon = $afternoon;
    }

    /**
     * @return Schedule
     */
    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }

    /**
     * @param Schedule $schedule
     * @return void
     */
    public function setSchedule(Schedule $schedule): void
    {
        $this->schedule = $schedule;
    }
}