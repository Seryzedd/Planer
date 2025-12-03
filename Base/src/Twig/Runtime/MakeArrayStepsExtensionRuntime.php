<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class MakeArrayStepsExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function getNumbersSteps(int $start, int $end, bool $reverse = false): array
    {
        $list = [];
        $step = 1;

        if($end >= 10 && $end < 1000) {
            $step = 100;
        } elseif ($end >= 1000 && $end < 10000) {
            $step = 500;
        } elseif ($end >= 10000 && $end < 50000) {
            $step = 5000;
        } else {
            $step = 100000;
        }

        $i = $start;

        while ($i <= $end) {
            $list[] = $i;
            $i = $i + $step;
        }

        $list[] = $i;

        if($reverse) {
            $list = array_reverse($list);
        }

        return $list;
    }
}
