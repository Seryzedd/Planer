<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use App\Entity\User\User;

class ScheduleManagerExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function isWorking(User $user)
    {
        $response = [];

        foreach($user->getSchedule()->getDays() as $day) {
            $response[$day->getName()] = [
                'AM' => $day->getMorning()->isWorking(),
                'PM' => $day->getAfternoon()->isWorking()
            ];
        }
        
        return $response;
    }
}
