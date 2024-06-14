<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use \DateTime;
use App\Entity\Work\Assignation;
use App\Entity\User\Schedule;

class ProjectCounterExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function calculateCounter(Assignation $assignation, ?string $month = null, ?string $year = null)
    {
        $today = new DateTime();

        if (!$month && !$year) {
            $today = date_create_from_format("d/n/Y", "01/" . $today->format('n') . "/" . $today->format('Y'));
        } else {
            $today = date_create_from_format("d/n/Y", "01/" . $month . "/" . $year);
        }
        
        $i = 0;
        $startDate = $assignation->getStartAt();

        while ($startDate < $today) {
            if ($startDate >= $today) {
                break;
            }

            if ($assignation->getId() === 12) {
                dump($i);
            }

            if ($i === $assignation->getDuration()) {
                break;
            }

            $availability = null;

            if (!$assignation->getUser()->getScheduleByDate($today)) {
                break;
            }

            foreach ($assignation->getUser()->getScheduleByDate($today)->getDays() as $day) {
                if ($day->getName() === $startDate->format('l')) {
                    $availability = $day;
                    break;
                }
            }

            if ($availability && $assignation->getUser()->isWorking($startDate->format('d/m/Y')) === false) {
                if ($availability->getMorning()->isWorking() && $availability->getAfternoon()->isWorking()) {
                    $i = $i + 1;
                } elseif ((!$availability->getMorning()->isWorking() && $availability->getAfternoon()->isWorking()) || ($availability->getMorning()->isWorking() && !$availability->getAfternoon()->isWorking())) {
                    $i = $i + 0.5;
                }
                // dump($assignation->getUser()->isWorking($startDate->format('d/m/Y')));
            }

            $startDate->modify('+1 day');
        }

        
        

        return min($i, $assignation->getDuration());
    }
}
