<?php

namespace Yggdrasil\Core\Driver;

use League\Container\Container;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;

class ContainerDriver implements DriverInterface
{
    private static $containerInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance(ConfigurationInterface $appConfiguration)
    {
        if(self::$containerInstance === null) {
            $container = new Container();
            $configuration = $appConfiguration->getConfiguration();

            if(array_key_exists('service', $configuration)) {
                foreach ($configuration['service'] as $name => $service) {
                    $container->add($name, $service)->withArgument($appConfiguration);
                }
            }

            self::$containerInstance = $container;
        }

        return self::$containerInstance;
    }
}