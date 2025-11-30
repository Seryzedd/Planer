<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use \DateTime;

class DateTimeTypeManagerRuntime implements RuntimeExtensionInterface
{

    public function getDateType(string $value = "now", string $format = "d/m/Y H:i", string $hour = "00", string $minutes = "00")
    {
        
        if ($value === 'now') {
            $date = new DateTime();
            $date->setTime((int) $hour, (int) $minutes);
        } else {
            $date = DateTime::createFromFormat($format, $value . ' ' . $hour . ':' . $minutes);
        }

        return $date;
    }

    public function getDaysThisMonth(): int
    {
        $number = 0;

        $date = new DateTime();
        return $this->days_in_month($date->format('m'), $date->format('Y'));
    }

    function days_in_month(int $month, int $year): int
    {
        // calculate number of days in a month
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }
}
