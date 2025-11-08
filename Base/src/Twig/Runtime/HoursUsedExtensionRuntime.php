<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use App\Entity\User\Day;

class HoursUsedExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function hoursUsedPerDemiDay(Day $day): array
    {
        $hours = [
            'AM' => $day->getMorning()->getHoursUsed(),
            'PM' => $day->getAfternoon()->getHoursUsed()
        ];

        return $hours;
    }
}
