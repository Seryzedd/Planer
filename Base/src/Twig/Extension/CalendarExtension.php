<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\CalendarExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use \Datetime;

class CalendarExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [CalendarExtensionRuntime::class, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('period', [$this, 'period']),
        ];
    }

    public function period(?string $month = null, ?string $year = null)
    {
        $date = new DateTime();

        if ($month) {
            if ($year) {
                $date = DateTime::createFromFormat('d/m/Y', '01/' . $month . '/' . $year);
            } else {
                $date = DateTime::createFromFormat('d/m/Y', '01/' . $month . '/' . $date->format('Y'));
            }

            $currentMonth = $date->format('m');
        } else {
            $date = DateTime::createFromFormat('d/m/Y', '01/' . $date->format('m') . '/' . $date->format('Y'));
            $currentMonth = $date->format('m');
        }

        $dates = [];
        while ($date->format('m') === $currentMonth) {
            $dates[] = clone $date;

            $date->modify('+1 day');
        }

        return $dates;
        
    }
}
