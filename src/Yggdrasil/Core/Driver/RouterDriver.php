<?php

namespace Yggdrasil\Core\Driver;

use Symfony\Component\Yaml\Yaml;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;
use Yggdrasil\Core\Routing\Router;
use Yggdrasil\Core\Routing\RoutingConfiguration;

/**
 * Class RouterDriver
 *
 * [Yggdrasil] Router driver, required in driver registry
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class RouterDriver implements DriverInterface
{
    /**
     * Instance of router
     *
     * @var Router
     */
    protected static $routerInstance;

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
            $requiredConfig = ['default_controller', 'default_action', 'controller_namespace', 'base_url', 'resource_path'];

            if (!$appConfiguration->isConfigured($requiredConfig, 'router')) {
                throw new MissingConfigurationException($requiredConfig, 'router');
            }

            $configuration = $appConfiguration->getConfiguration();

            $passiveActions = Yaml::parseFile(dirname(__DIR__, 7) . '/src/' . $configuration['router']['resource_path'] . '/passive_actions.yaml');

            $routingConfig = (new RoutingConfiguration())
                ->setBaseUrl($configuration['router']['base_url'])
                ->setControllerNamespace($configuration['router']['controller_namespace'])
                ->setDefaultController($configuration['router']['default_controller'])
                ->setDefaultAction($configuration['router']['default_action'])
                ->setPassiveActions($passiveActions ?? ['passive_actions' => []]);

            $router = new Router($routingConfig);

            self::$routerInstance = $router;
        }

        return self::$routerInstance;
    }
}
