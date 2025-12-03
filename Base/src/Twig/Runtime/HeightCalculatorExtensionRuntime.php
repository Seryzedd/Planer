<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class HeightCalculatorExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function getHeightPercent(int $value, int $max): int
    {
        return ($value / $max) *100;
    }
}
