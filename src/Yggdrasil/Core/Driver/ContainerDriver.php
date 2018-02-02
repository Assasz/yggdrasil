<?php

namespace Yggdrasil\Core\Driver;

use League\Container\Container;

class ContainerDriver implements DriverInterface
{
    private static $containerInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance($configuration)
    {
        if(self::$containerInstance === null) {
            $container = new Container();

            foreach ($configuration['service'] as $name => $service){
                $container->add($name, $service);
            }

            self::$containerInstance = $container;
        }

        return self::$containerInstance;
    }
}