<?php

namespace Yggdrasil\Core\Driver;

use League\Container\Container;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class ContainerDriver
 *
 * [Symfony Dependency Injection] DI Container driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class ContainerDriver implements DriverInterface
{
    /**
     * Instance of container
     *
     * @var ContainerBuilder
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
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure container
     * @return ContainerBuilder
     *
     * @throws MissingConfigurationException if services_path is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): ContainerBuilder
    {
        if (self::$containerInstance === null) {
            $container = new ContainerBuilder();
            $configuration = $appConfiguration->getConfiguration();

            if (!$appConfiguration->isConfigured(['services_path'], 'container')) {
                throw new MissingConfigurationException('There is missing parameter in your configuration: services_path in container section.');
            }

            $servicesPath = dirname(__DIR__, 7) . '/src/ ' . $configuration['container']['services_path'];

            $loader = new YamlFileLoader($container, new FileLocator($servicesPath));
            $loader->load('services.yaml');

            self::$containerInstance = $container;
        }

        return self::$containerInstance;
    }
}
