<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use \DateTime;

class DateTimeTypeManagerRuntime implements RuntimeExtensionInterface
{

    public function getDateType(string $value = "now", string $format = "d/m/Y")
    {
        if ($value === 'now') {
            $date = new DateTime();
        } else {
            $date = DateTime::createFromFormat($format, $value);
        }

        return $date;
    }
}
