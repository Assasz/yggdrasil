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
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure entity manager
     * @return EntityManager
     *
     * @throws MissingConfigurationException if db_name, db_host, db_user, db_password or entity_namespace are not configured
     * @throws DBALException
     * @throws ORMException
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): EntityManager
    {
        if (self::$managerInstance === null) {
            $configuration = $appConfiguration->getConfiguration();
            $requiredConfig = ['db_name', 'db_user', 'db_password', 'db_host', 'entity_namespace'];

            if (!$appConfiguration->isConfigured($requiredConfig, 'entity_manager')) {
                throw new MissingConfigurationException('There are missing parameters in your configuration: db_name, db_user, db_password, db_host or entity_namespace in section entity_manager.');
            }

            $connectionParams = [
                'dbname' => $configuration['entity_manager']['db_name'],
                'user' => $configuration['entity_manager']['db_user'],
                'password' => $configuration['entity_manager']['db_password'],
                'host' => $configuration['entity_manager']['db_host'],
                'port' => $configuration['entity_manager']['db_port'] ?? 3306,
                'driver' => $configuration['entity_manager']['db_driver'] ?? 'pdo_mysql',
                'charset' => $configuration['entity_manager']['db_charset'] ?? 'UTF8'
            ];

            $entityPath = implode('/', explode('\\', $configuration['entity_manager']['entity_namespace']));
            $entityPath = [dirname(__DIR__, 7) . '/src/' . $entityPath . '/'];

            $config = Setup::createAnnotationMetadataConfiguration($entityPath);
            $config->addEntityNamespace('Entity', $configuration['entity_manager']['entity_namespace']);

            $connection = DriverManager::getConnection($connectionParams, $config);
            $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            self::$managerInstance = EntityManager::create($connection, $config);
        }

        return self::$managerInstance;
    }
}
