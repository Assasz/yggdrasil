<?php

namespace Yggdrasil\Core\Driver;

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
abstract class ContainerDriver implements DriverInterface
{
    /**
     * Instance of container
     *
     * @var ContainerBuilder
     */
    protected static $containerInstance;

    /**
     * Returns instance of container
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure container
     * @return ContainerBuilder
     *
     * @throws MissingConfigurationException if resource_path is not configured
     * @throws \Exception
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): ContainerBuilder
    {
        if (self::$containerInstance === null) {
            $container = new ContainerBuilder();
            $configuration = $appConfiguration->getConfiguration();

            if (!$appConfiguration->isConfigured(['resource_path'], 'container')) {
                throw new MissingConfigurationException('There is missing parameter in your configuration: resource_path in container section.');
            }

            $resourcePath = dirname(__DIR__, 7) . '/src/' . $configuration['container']['resource_path'];

            $loader = new YamlFileLoader($container, new FileLocator($resourcePath));
            $loader->load('services.yaml');

            $container->setParameter('app.configuration', $appConfiguration);

            self::$containerInstance = $container;
        }

        return self::$containerInstance;
    }
}
