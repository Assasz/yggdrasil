<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Driver\DriverInterface;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class ExceptionHandlerDriver implements DriverInterface
{
    private static $driverInstance;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance($configuration)
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