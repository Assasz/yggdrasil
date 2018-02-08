<?php

namespace Yggdrasil\Core\Driver;

use AppModule\Infrastructure\Config\AppConfiguration;
use Yggdrasil\Core\Routing\Router;
use Yggdrasil\Core\Driver\Base\DriverInterface;

class RoutingDriver implements DriverInterface
{
    private static $routerInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance(AppConfiguration $appConfiguration)
    {
        if(self::$routerInstance === null){
            $configuration = $appConfiguration->getConfiguration();
            $router = new Router();

            if(!$appConfiguration->isConfigured(['default_controller', 'default_action'], 'routing')){
                //exception
            }

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