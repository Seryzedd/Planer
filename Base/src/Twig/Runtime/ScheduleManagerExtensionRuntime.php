<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use App\Entity\User\User;
use App\Entity\User\Schedule;
use \DateTime;

class ScheduleManagerExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function isWorking(User $user, DateTime $date)
    {
        $response = [];

        $schedule = $user->getScheduleByDate($date);

        if ($schedule && $schedule->getStartAt() <= $date) {
            foreach($schedule->getDays() as $day) {
                $response[$day->getName()] = [
                    'AM' => $day->getMorning()->isWorking(),
                    'PM' => $day->getAfternoon()->isWorking()
                ];
            }
        } else {
            foreach (Schedule::WEEK_DAYS as $day) {
                $response[$day] = [
                        'AM' => false,
                        'PM' => false
                    ]
                ;
            }
        }
        
        return $response;
    }
}
