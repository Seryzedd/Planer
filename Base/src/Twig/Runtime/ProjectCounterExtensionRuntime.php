<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use \DateTime;
use App\Entity\Work\Assignation;

class ProjectCounterExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function calculateCounter(Assignation $assignation, ?string $month)
    {
        $today = new DateTime();
        if ($month === null) {
            $month = $today->format('n');
        }

        

        $days = $assignation->getUser()->getSchedule()->getDays()->toArray();
        
        $i = 0;
        $startDate = $assignation->getStartAt();
        if ($startDate < $today) {
            while ($startDate->format('n') !== $month) {
                $startDate->modify('+1 day');

                $availability = null;
                foreach ($days as $day) {
                    if ($day->getName() === $startDate->format('l')) {
                        $availability = $day;
                        break;
                    }
                }

                if ($availability) {
                    if ($availability->getMorning()->isWorking() && $availability->getAfternoon()->isWorking()) {
                        $i = $i + 1;
                    } elseif ((!$availability->getMorning()->isWorking() && $availability->getAfternoon()->isWorking()) || ($availability->getMorning()->isWorking() && !$availability->getAfternoon()->isWorking())) {
                        $i = $i + 0.5;
                    }
                }
            }
        }
        return min($i, $assignation->getDuration());
    }
}
