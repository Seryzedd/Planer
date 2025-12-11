<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class FilterDataExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function makeFilterParameters(array $params): string
    {
        $response = "";
        foreach($params as $key => $param) {
            if (is_string($param) || is_int($param)) {
                $response .= $param;
                $response .= '//';
            }
        }

        return base64_encode($response);
    }
}
