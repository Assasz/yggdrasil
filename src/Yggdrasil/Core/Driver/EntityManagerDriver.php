<?php

namespace Yggdrasil\Core\Driver;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

class EntityManagerDriver implements DriverInterface
{
    private static $managerInstance;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance(ConfigurationInterface $appConfiguration)
    {
        if(self::$managerInstance === null) {
            $config = new Configuration();
            $configuration = $appConfiguration->getConfiguration();

            if(!$appConfiguration->isConfigured(['name', 'user', 'password', 'host'], 'database')){
                throw new MissingConfigurationException('There are missing parameters in your configuration. name, user, password and host are required to connect to database.');
            }

            $connectionParams = [
                'dbname' => $configuration['database']['name'],
                'user' => $configuration['database']['user'],
                'password' => $configuration['database']['password'],
                'host' => $configuration['database']['host'],
                'port' => $configuration['database']['port'] ?? 3306,
                'driver' => $configuration['database']['driver'] ?? 'pdo_mysql',
                'charset' => $configuration['database']['charset'] ?? 'UTF8'
            ];

            $entityPaths = [dirname(__DIR__, 7) . '/src/'.$configuration['application']['entity_path'].'/'];
            $config = Setup::createAnnotationMetadataConfiguration($entityPaths, true);
            $config->addEntityNamespace('Entity', 'AppModule\Domain\Entity');

            $connection = DriverManager::getConnection($connectionParams, $config);
            $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            self::$managerInstance = EntityManager::create($connection, $config);
        }

        return self::$managerInstance;
    }
}