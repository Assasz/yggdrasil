<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class CacheDriver
 *
 * [Redis] Cache mechanism driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class CacheDriver implements DriverInterface
{
    /**
     * Instance of cache
     *
     * @var \Redis
     */
    protected static $cacheInstance;

    /**
     * Returns instance of cache
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure cache
     * @return \Redis
     *
     * @throws MissingConfigurationException if redis_host or redis_port is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): \Redis
    {
        if (self::$cacheInstance === null) {
            $configuration = $appConfiguration->getConfiguration();

            if (!$appConfiguration->isConfigured(['redis_host', 'redis_port'], 'cache')) {
                throw new MissingConfigurationException('There are missing parameters in your configuration: redis_host or redis_port in section cache.');
            }

            $redis = new \Redis();
            $redis->connect(
                $configuration['cache']['redis_host'],
                $configuration['cache']['redis_port']
            );

            self::$cacheInstance = $redis;
        }

        return self::$cacheInstance;
    }
}