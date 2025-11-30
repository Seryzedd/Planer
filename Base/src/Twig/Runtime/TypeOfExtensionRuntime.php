<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class TypeOfExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function isTypeOf(mixed $value, string $type): bool
    {
        return gettype($value) == $type;
    }
}
