<?php

namespace App\Service;

use \DateTime;

class CostsCalculator
{
    public function calculateTotalCostsPerYear(array $costs, int $startYear = 2000): float
    {
        $total = 0.0;

        $result = [];

        foreach (range($startYear, $end +10, 1) as $year) {
            foreach(range(1, 12, 1) as $month) {
                $date = DateTime($year . '-' . $month . '-01');

                for($day = 1; $day <=31; $day++) {
                    if($day >= cal_days_in_month(CAL_GREGORIAN, $month, $year)) {
                        break;
                    }

                    $result[$date->format('Y')][$date->format('F')][$date->format('W')][$date->format('l')] = 0;

                    $date->modify('+1 day');
                }
            }
        }

        dump($result);

        return $result;
    }
}