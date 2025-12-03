<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class NumberCheckerExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function isModulo(int $value, int $compare): bool
    {
        return ($value % $compare) === 0;
    }
}
