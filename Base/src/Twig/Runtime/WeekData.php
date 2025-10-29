<?php

namespace App\Twig\Runtime;

use \DateTime;

class WeekData
{
    private string $month = '';

    private int $weekNumber = 0;

    private int $year;

    private int $monthNumber;

    private array $days = [];

    public function __construct($date)
    {
        $this->month = $date->format('F');
        $this->weekNumber = $date->format('W');
        $this->year = $date->format('Y');
        $this->monthNumber = $date->format('m');
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function getWeekNumber()
    {
        return $this->weekNumber;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonthNumber(): int
    {
        return $this->monthNumber;
    }

    public function addDay(DateTime $date)
    {
        $this->days[] = $date;

        return $this;
    }

    public function getDays(): array
    {
        return $this->days;
    }
}