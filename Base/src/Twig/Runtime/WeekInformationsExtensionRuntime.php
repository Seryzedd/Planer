<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use \DateTime;
use App\Twig\Runtime\WeekData;
use App\Entity\User\User;

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

    public function getWeeks(string $value, User $user): array
    {
        $weeks = [];

        $date = DateTime::createFromFormat('d-m-Y H:i', $value . ' 00:00');

        $date->modify('first monday of previous month');

        for ($w = 0; $w <= 35; $w++) {
            $data = new WeekData(clone $date);

            for ($i = 1; $i <= 7; $i++) {
                $data->addDay(clone $date);

                $events = $user->getEventByDate($date);
                
                $weeks[$data->getYear()][$data->getMonth()][$data->getWeekNumber()][$date->format('d')] = $user->getEventByDate($date);

                $date->modify('+1 day');
            }
        }

        return $weeks;
    }
}
