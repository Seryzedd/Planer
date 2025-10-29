<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\LocalTimeZoneManagerRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LocalTimeZoneManagerExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('TimeZone', [LocalTimeZoneManagerRuntime::class, 'TimeZone']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [LocalTimeZoneManagerRuntime::class, 'doSomething']),
        ];
    }
}
