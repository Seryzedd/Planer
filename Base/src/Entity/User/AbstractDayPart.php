<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractEntity;

#[ORM\MappedSuperclass]
abstract class AbstractDayPart extends AbstractEntity
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

    public function getHoursUsed()
    {
        if(!$this->isWorking()) {
            return 0;
        }
        $minutesEnd = strtotime($this->endHour . ':' . $this->endMinutes . ":00");
        $minutesStart = strtotime($this->startHour . ':' . $this->startMinutes . ":00");

        $diff = $minutesEnd - $minutesStart;

        return $diff / 60 /60;
    }
}
