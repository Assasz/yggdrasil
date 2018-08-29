<?php

namespace Yggdrasil\Core\Driver;

use Symfony\Component\Yaml\Yaml;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;
use Yggdrasil\Core\Routing\Router;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Routing\RoutingConfiguration;

/**
 * Class RouterDriver
 *
 * [Yggdrasil] Router driver, required in driver registry
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
    protected static $routerInstance;

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
     * @throws MissingConfigurationException if default_controller, default_action, controller_namespace, base_url or resource_path are not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): Router
    {
        if (self::$routerInstance === null) {
            $configuration = $appConfiguration->getConfiguration();
            $requiredConfig = ['default_controller', 'default_action', 'controller_namespace', 'base_url', 'resource_path'];

            if (!$appConfiguration->isConfigured($requiredConfig, 'router')) {
                throw new MissingConfigurationException('There are missing parameters in your configuration: default_controller, default_action, controller_namespace, base_url or resource_path in router section.');
            }

            $passiveActions = Yaml::parseFile($configuration['router']['resource_path'] . '/passive_actions.yaml');

            $routingConfig = (new RoutingConfiguration())
                ->setBaseUrl($configuration['router']['base_url'])
                ->setControllerNamespace($configuration['router']['controller_namespace'])
                ->setDefaultController($configuration['router']['default_controller'])
                ->setDefaultAction($configuration['router']['default_action'])
                ->setPassiveActions($passiveActions);

            $router = new Router($routingConfig);

            self::$routerInstance = $router;
        }

        return self::$routerInstance;
    }
}
