<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Routing\Router;

class RoutingDriver implements DriverInterface
{
    private static $routerInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance($configuration)
    {
        if(self::$routerInstance === null){
            $router = new Router();
            $defaults = [
                'controller' => $configuration['routing']['default_controller'],
                'action' => $configuration['routing']['default_action']
            ];

            $router->setDefaults($defaults);

            self::$routerInstance = $router;
        }

        return self::$routerInstance;
    }
}