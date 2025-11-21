<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\Routing\RouterInterface;

class RouteManagerExtensionRuntime implements RuntimeExtensionInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getAdminRoutes()
    {
        $routes = [];

        $collection = $this->router->getRouteCollection();
        $allRoutes = $collection->all();

        foreach ($allRoutes as $name => $route) {
            $defaults = $route->getDefaults();

            if (array_key_exists('admin', $defaults) && $defaults['admin'] === true) {
                $routes[$name] = $route;
            }
        }

        return $routes;
    }
}
