<?php

namespace Yggdrasil\Core\Driver;

use Doctrine\Common\Cache\{MemcachedCache, RedisCache};
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
 * [Doctrine ORM] Entity Manager driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class EntityManagerDriver implements DriverInterface
{
    /**
     * Instance of entity manager
     *
     * @var EntityManager
     */
    protected static $managerInstance;

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
            $requiredConfig = ['db_name', 'db_user', 'db_password', 'db_host', 'entity_namespace'];

            if (!$appConfiguration->isConfigured($requiredConfig, 'entity_manager')) {
                throw new MissingConfigurationException($requiredConfig, 'entity_manager');
            }

            $configuration = $appConfiguration->getConfiguration();

            $connectionParams = [
                'dbname'   => $configuration['entity_manager']['db_name'],
                'user'     => $configuration['entity_manager']['db_user'],
                'password' => $configuration['entity_manager']['db_password'],
                'host'     => $configuration['entity_manager']['db_host'],
                'port'     => $configuration['entity_manager']['db_port']    ?? 3306,
                'driver'   => $configuration['entity_manager']['db_driver']  ?? 'pdo_mysql',
                'charset'  => $configuration['entity_manager']['db_charset'] ?? 'UTF8'
            ];

            $entityPath = [dirname(__DIR__, 7) . '/src/' . $configuration['entity_manager']['entity_namespace'] . '/'];

            $config = Setup::createAnnotationMetadataConfiguration($entityPath);
            $config->addEntityNamespace('Entity', $configuration['entity_manager']['entity_namespace']);

            if (!DEBUG && $appConfiguration->hasDriver('cache')) {
                $cacheDriver = self::getCacheDriver($appConfiguration);

                $config->setQueryCacheImpl($cacheDriver);
                $config->setResultCacheImpl($cacheDriver);
                $config->setMetadataCacheImpl($cacheDriver);
            }

            $connection = DriverManager::getConnection($connectionParams, $config);
            $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            self::$managerInstance = EntityManager::create($connection, $config);
        }

        return self::$managerInstance;
    }

    /**
     * Returns cache driver for entity manager configuration
     *
     * @param ConfigurationInterface $appConfiguration
     * @return RedisCache
     */
    protected static function getCacheDriver(ConfigurationInterface $appConfiguration): RedisCache
    {
        $redis = $appConfiguration->loadDriver('cache');

        $cacheDriver = new RedisCache();
        $cacheDriver->setRedis($redis);

        return $cacheDriver;
    }
}
