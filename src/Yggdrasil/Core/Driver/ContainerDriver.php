<?php

namespace Yggdrasil\Core\Driver;

use League\Container\Container;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class ContainerDriver
 *
 * [The PHP League] Dependency Injection Container driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class ContainerDriver implements DriverInterface
{
    /**
     * Instance of container
     *
     * @var Container
     */
    protected static $containerInstance;

    /**
     * ContainerDriver constructor.
     *
     * Should be private to prevent object creation. Same with __clone
     */
    private function __construct() {}

    private function __clone() {}

    /**
     * Returns instance of container
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to get registered services
     * @return Container
     *
     * @throws MissingConfigurationException if service_namespace is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): Container
    {
        if (self::$containerInstance === null) {
            $container = new Container();
            $configuration = $appConfiguration->getConfiguration();

            if (!$appConfiguration->isConfigured(['service_namespace'], 'container')) {
                throw new MissingConfigurationException('There is missing parameter in your configuration: service_namespace in container section.');
            }

            $serviceNamespace = $configuration['container']['service_namespace'];
            unset($configuration['container']['service_namespace']);

            foreach ($configuration['container'] as $name => $service) {
                $container
                    ->add($name, $serviceNamespace . $service)
                    ->withArgument($appConfiguration);
            }

            self::$containerInstance = $container;
        }

        return self::$containerInstance;
    }
}
