<?php

namespace Yggdrasil\Core\Driver;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Yggdrasil\Core\Driver\DriverInterface;

class DbDriver implements DriverInterface
{
    private static $connectionInstance;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance($configuration)
    {
        if(self::$connectionInstance === null) {
            $config = new Configuration();
            $connectionParams = [
                'dbname' => $configuration['db.db_name'],
                'user' => $configuration['db.user'],
                'password' => $configuration['db.password'],
                'host' => $configuration['db.host'],
                'driver' => $configuration['db.driver'],
                'charset' => $configuration['db.charset']
            ];

            self::$connectionInstance = DriverManager::getConnection($connectionParams, $config);
        }

        return self::$connectionInstance;
    }
}