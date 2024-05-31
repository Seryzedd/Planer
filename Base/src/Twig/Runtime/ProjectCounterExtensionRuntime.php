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
        }

        if ($month === null) {
            $month = $today->format('n');
        }
        
        $days = Schedule::WEEK_DAYS;
        
        $i = 0;
        $startDate = $assignation->getStartAt();
        dump($today);
        while ($today > $startDate) {
            $availability = null;
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
            
            }

            $today->modify('+1 day');
        }

        return min($i, $assignation->getDuration());
    }
}
