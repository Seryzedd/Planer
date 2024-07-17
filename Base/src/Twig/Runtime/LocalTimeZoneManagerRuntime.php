<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class LocalTimeZoneManagerRuntime implements RuntimeExtensionInterface
{

    public function TimeZone(string $value)
    {
        switch ($value) {
            case "en":
                $timezone = "Europe/London";
                break;
            case "fr":
                $timezone = "Europe/Paris";
                break;
            default:
                $timezone = false;
        }
        return $timezone;
    }
}
