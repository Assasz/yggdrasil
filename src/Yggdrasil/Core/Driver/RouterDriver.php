<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;
use Yggdrasil\Core\Routing\Router;
use Yggdrasil\Core\Driver\Base\DriverInterface;

/**
 * Class RouterDriver
 *
 * Router driver, necessary for routing to work
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class RouterDriver implements DriverInterface
{
    /**
     * Instance of router
     *
     * @var Router
     */
    private static $routerInstance;

    /**
     * RouterDriver constructor.
     *
     * Should be private to prevent object creation. Same with __clone
     */
    private function __construct(){}

    private function __clone(){}

    /**
     * Returns instance of router
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure router
     * @return Router
     *
     * @throws MissingConfigurationException if default controller, action and controller namespace are not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): Router
    {
        if(self::$routerInstance === null){
            $configuration = $appConfiguration->getConfiguration();
            $router = new Router();

            if(!$appConfiguration->isConfigured(['default_controller', 'default_action', 'controller_namespace'], 'routing')){
                throw new MissingConfigurationException('There are missing parameters in your configuration. default_controller, default_action and controller_namespace are required for router to work properly.');
            }

            $defaults = [
                'controller' => $configuration['routing']['default_controller'],
                'action' => $configuration['routing']['default_action']
            ];

            $router->setDefaults($defaults);
            $router->setControllerNamespace($configuration['routing']['controller_namespace']);

            self::$routerInstance = $router;
        }

        return self::$routerInstance;
    }
}