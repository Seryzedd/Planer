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
}
