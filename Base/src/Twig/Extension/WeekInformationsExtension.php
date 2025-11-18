<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\WeekInformationsExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class WeekInformationsExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('week', [WeekInformationsExtensionRuntime::class, 'getWeek']),
            new TwigFunction('weeks', [WeekInformationsExtensionRuntime::class, 'getWeeks'])
        ];
    }
}
