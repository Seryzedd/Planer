<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use \DateTime;
use App\Twig\Runtime\WeekData;

class WeekInformationsExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function getWeek(string $value): WeekData
    {

        $date = DateTime::createFromFormat('d-m-Y H:i', $value . ' 00:00');

        $date->modify('Midnight Monday this week');

        $data = new WeekData($date);

        for ($i = 1; $i <= 7; $i++) {
            $data->addDay(clone $date);

            $date->modify('+1 day');
        }

        return $data;
    }
}
