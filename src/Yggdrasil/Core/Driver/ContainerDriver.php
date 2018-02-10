<?php

namespace Yggdrasil\Core\Driver;

use League\Container\Container;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class ContainerDriver
 *
 * Container driver, necessary for services to work
 * League\Container is framework default container
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
    private static $containerInstance;

    /**
     * ContainerDriver constructor.
     *
     * Should be private to prevent object creation. Same with __clone
     */
    private function __construct(){}

    private function __clone(){}

    /**
     * Returns instance of container
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to get registered services
     * @return Container
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): Container
    {
        if(self::$containerInstance === null) {
            $container = new Container();
            $configuration = $appConfiguration->getConfiguration();

            if(!$appConfiguration->isConfigurder(['service_namespace'], 'application')){
                throw new MissingConfigurationException('There are missing parameters in your configuration. service_namespace is required for services to work.');
            }

            if(array_key_exists('service', $configuration)) {
                foreach ($configuration['service'] as $name => $service) {
                    $container->add($name, $configuration['application']['service_namespace'].$service)->withArgument($appConfiguration);
                }
            }

            self::$containerInstance = $container;
        }

        return self::$containerInstance;
    }
}