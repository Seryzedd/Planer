<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use App\Entity\Client\Project;
use App\Entity\User\User;

class SoldedDaysProjectExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function calculateSoldedDaysLeft(Project $project): array
    {
        $sold = [];

        $jobs = User::JOBS;

        foreach($jobs as $job) {
            $sold[$job] = 0;
        }

        foreach($project->getHoursSold() as $hours) {
            if (!isset($sold[$hours->getType()])) {
                $sold[$hours->getType()] = 0.0;
            }

            $sold[$hours->getType()] = (float) $sold[$hours->getType()] + $hours->getDelay();
        }

        foreach ($project->getAssignations() as $assignation) {
            if (isset($sold[$assignation->getUser()->getJob()])) {
                $sold[$assignation->getUser()->getJob()] = (float) max(0, $sold[$assignation->getUser()->getJob()] - $assignation->getDuration());
            }
        }

        return $sold;
    }
}
