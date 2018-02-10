<?php

namespace Yggdrasil\Core\Driver;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class EntityManagerDriver
 *
 * Entity manager driver, necessary for ORM to work
 * Doctrine is framework default ORM
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class EntityManagerDriver implements DriverInterface
{
    /**
     * Instance of entity manager
     *
     * @var EntityManager
     */
    private static $managerInstance;

    /**
     * EntityManagerDriver constructor.
     *
     * Should be private to prevent object creation. Same with __clone
     */
    private function __construct() {}

    private function __clone() {}

    /**
     * Returns instance of entity manager
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to connect to database and configure entity manager
     * @return EntityManager
     *
     * @throws MissingConfigurationException if name, host, user and password of database or entity_path are not configured
     * @throws DBALException
     * @throws ORMException
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): EntityManager
    {
        if(self::$managerInstance === null) {
            $configuration = $appConfiguration->getConfiguration();

            if(!$appConfiguration->isConfigured(['name', 'user', 'password', 'host'], 'database')){
                throw new MissingConfigurationException('There are missing parameters in your configuration. name, user, password and host are required to connect to database.');
            }

            if(!$appConfiguration->isConfigured(['entity_path'], 'application')){
                throw new MissingConfigurationException('There are missing parameters in your configuration. entity_path is required for entity manager to work.');
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
            $entityNamespace = implode('\\', explode('/', $configuration['application']['entity_path']));

            $config = Setup::createAnnotationMetadataConfiguration($entityPaths, DEBUG);
            $config->addEntityNamespace('Entity', $entityNamespace);

            $connection = DriverManager::getConnection($connectionParams, $config);
            $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            self::$managerInstance = EntityManager::create($connection, $config);
        }

        return self::$managerInstance;
    }
}