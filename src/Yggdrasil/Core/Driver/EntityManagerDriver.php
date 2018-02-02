<?php

namespace Yggdrasil\Core\Driver;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Yggdrasil\Core\Driver\DriverInterface;

class EntityManagerDriver implements DriverInterface
{
    private static $managerInstance;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance($configuration)
    {
        if(self::$managerInstance === null) {
            $config = new Configuration();
            $connectionParams = [
                'dbname' => $configuration['database']['name'],
                'user' => $configuration['database']['user'],
                'password' => $configuration['database']['password'],
                'host' => $configuration['database']['host'],
                'driver' => $configuration['database']['driver'],
                'charset' => $configuration['database']['charset']
            ];

            $entityPaths = [dirname(__DIR__, 6) . '/src/AppModule/Domain/Entity/'];
            $config = Setup::createAnnotationMetadataConfiguration($entityPaths, true);
            $config->addEntityNamespace('Entity', 'AppModule\Domain\Entity');

            $connection = DriverManager::getConnection($connectionParams, $config);

            self::$managerInstance = EntityManager::create($connection, $config);
        }

        return self::$managerInstance;
    }
}