<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class InstanceOfExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function isInstanceOf(mixed $value, string $type): bool
    {
        return $value instanceof $type;
    }
}
