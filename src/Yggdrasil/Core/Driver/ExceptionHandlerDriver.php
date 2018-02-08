<?php

namespace Yggdrasil\Core\Driver;

use AppModule\Infrastructure\Config\AppConfiguration;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class ExceptionHandlerDriver implements DriverInterface
{
    private static $driverInstance;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance(AppConfiguration $appConfiguration)
    {
        if(self::$driverInstance === null) {
            $driver = new Run();
            $driver->pushHandler(new PrettyPageHandler());
            $driver->register();

            self::$driverInstance = $driver;
        }

        return self::$driverInstance;
    }
}