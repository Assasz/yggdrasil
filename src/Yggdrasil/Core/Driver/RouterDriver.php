<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;
use Yggdrasil\Core\Routing\Router;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Routing\RoutingConfiguration;

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
    private function __construct() {}

    private function __clone() {}

    /**
     * Returns instance of router
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure router
     * @return Router
     *
     * @throws MissingConfigurationException if default controller, action or controller namespace are not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): Router
    {
        if (self::$routerInstance === null) {
            $configuration = $appConfiguration->getConfiguration();
            $requiredConfig = ['default_controller', 'default_action', 'controller_namespace', 'base_url'];

            if (!$appConfiguration->isConfigured($requiredConfig, 'router')) {
                throw new MissingConfigurationException('There are missing parameters in your configuration: default_controller, default_action, controller_namespace or base_url in router section.');
            }

            $routingConfig = (new RoutingConfiguration())
                ->setBaseUrl($configuration['router']['base_url'])
                ->setControllerNamespace($configuration['router']['controller_namespace'])
                ->setDefaultController($configuration['router']['default_controller'])
                ->setDefaultAction($configuration['router']['default_action']);

            $router = new Router($routingConfig);

            self::$routerInstance = $router;
        }

        return self::$routerInstance;
    }
}
