<?php

namespace App\Service;

use \DateTime;
use App\Service\DateGenerator;
use App\Entity\Work\Assignation;

class CostsCalculator
{
    private DateGenerator $dateGenerator;

    public function __construct(DateGenerator $dateGenerator)
    {
        $this->dateGenerator = $dateGenerator;
    }

    public function calculateTotalCostsClients(array $clients, ?int $year = null): array
    {
        if (!$year) {
            $date = new DateTime();
            $year = (int) $date->format('Y');
        }
        $total = 0.0;

        $result = [
            $year => []
        ];
        
        $maxCost = 0;
        $maxDuration = 0;

        $totals = $this->getTotalValues($clients);

        foreach($this->dateGenerator->getMonthList() as $monthNum => $month) {
            $result[$year][$month] = [];
            $date = new DateTime('01/' . $monthNum . '/' . $year);

            $totalDuration = 0;
            $totalCost = 0;

            for ($i=1; $i < 31; $i++) { 
                if($i <= (int) $date->format('t')) {
                    $clientTotal = 0;
                    foreach($clients as $client) {
                        
                        foreach($client->getProjects() as $project) {
                            $iter = $totals[$client->getId()]['projects'][$project->getId()];
                            foreach($project->getAssignations() as $assignation) {
                                if($assignation->getStartAt()->format('F/Y') === $month . '/' . $year)
                                {
                                    if($iter <= 0) {
                                        break;
                                    }
                                    
                                    $iter = max(0, $iter - $assignation->getDuration());
                                    
                                    $user = $assignation->getUser();
                                    $cost = $user->getCostByDate($date);
                                    $hourPerDay = $user->getHoursForDay($date, $date->format('l'));

                                    $totalDuration += $assignation->getDuration();
                                    $totalCost += $cost->getPrice() * $hourPerDay;

                                    $clientTotal += $totalCost;

                                    $result[$year][$month][$client->getName()][$project->getName() !== '' ? $project->getName() : 'anonymous'] = [
                                        'cost' => $totalCost,
                                        'duration' => $totalDuration
                                    ];

                                    if ($totalCost > $maxCost) {
                                        $maxCost = $totalCost;
                                    }
                                    
                                    if($totalDuration > $maxDuration) {
                                        $maxDuration = $totalDuration;
                                    }

                                }
                            }
                        }
                        $result[$year][$month][$client->getName()]['totalCost'] = $clientTotal;
                    }
                }

                $date->modify('+1 day');
            }
        }

        $result['maxCost'] = $maxCost;
        $result['maxDuration'] = $maxDuration;

        return $result;
    }

    public function getUsersAssignationsDatas(array $users, ?int $year = null): array
    {
                if ($year) {
            $date = new DateTime("01-01-" . $year);
        } else {
            $date = new DateTime('first day of january');
        }

        $year = $date->format('Y');

        $result = [
            $year => []
        ];

        $result[$year]= [];

        $assignationsChecked = [];

        foreach($users as $user) {
            $priceTotal = 0;
            $assignationsCount = 0;

            $currentMonth = $date->format('F');
            while ((int) $date->format('Y') === (int) $year) {
                foreach($user->getAssignations() as $assignation) {

                    if ($date >= $assignation->getStartAt() && $assignationsCount <= $assignation->getDuration()) {
                        if (in_array($assignation->getId(), $assignationsChecked) === false) {
                            $assignationsCount++;
                            
                            $cost = $user->getCostByDate($date);
                            
                            $hourPerDay = $user->getHoursForDay($date, $date->format('l'));

                            $priceTotal += $cost->getPrice() * $hourPerDay;

                            $assignationsChecked[] = $assignation->getId();
                        }
                    }
                }

                $result[$year][$date->format('F')][$user->getId()] = [
                    'assignations' => $assignationsCount,
                    'price' => $priceTotal,
                    'user' => $user->toArray()
                ];

                if ($date->format('d') === $date->format('t')) {
                    $priceTotal = 0;
                    $assignationsCount = 0;
                }

                $date->modify('+1 day');
            }
        }

        dump($result);

        return $result;
    }

    private function getAssignationDurationInMonth(string $month, Assignation $assignation): int
    {
        if ($assignation->getStartAt()->format('m') === $month) {
            return $assignation->getDuration();
        }
        return 0;
    }

    private function getTotalValues(array $clients): array
    {
        $response = [];
        foreach($clients as $client) {
            $totalDuration = 0;
            foreach($client->getProjects() as $project) {
                $duration = 0;
                foreach($project->getAssignations() as $assignation) {
                    $duration += $assignation->getDuration();
                }
                $response[$client->getId()]['projects'][$project->getId()] = $duration;
            }
            $response[$client->getId()]['total'] = $totalDuration;
        }
        return $response;
    }
}