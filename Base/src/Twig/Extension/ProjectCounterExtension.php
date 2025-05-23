<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ProjectCounterExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ProjectCounterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('calculateCounter', [ProjectCounterExtensionRuntime::class, 'calculateCounter']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [ProjectCounterExtensionRuntime::class, 'doSomething']),
        ];
    }
}
