<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ArrayManagerRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ArrayManagerExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('current', [ArrayManagerRuntime::class, 'getCurrent']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [ArrayManagerRuntime::class, 'doSomething']),
        ];
    }
}
