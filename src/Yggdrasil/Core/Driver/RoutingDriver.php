<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;
use Yggdrasil\Core\Routing\Router;
use Yggdrasil\Core\Driver\Base\DriverInterface;

class RoutingDriver implements DriverInterface
{
    private static $routerInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance(ConfigurationInterface $appConfiguration)
    {
        if(self::$routerInstance === null){
            $configuration = $appConfiguration->getConfiguration();
            $router = new Router();

            if(!$appConfiguration->isConfigured(['default_controller', 'default_action'], 'routing')){
                throw new MissingConfigurationException('There are missing parameters in your configuration. default_controller and default_action are required for routing to work properly.');
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