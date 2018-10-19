<?php

namespace Yggdrasil\Component\NidhoggComponent\Driver;

use Yggdrasil\Component\NidhoggComponent\Routing\RouteCollector;
use Yggdrasil\Component\NidhoggComponent\WampServer;
use Yggdrasil\Component\NidhoggComponent\WampServerAdapter;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class WampServerDriver
 *
 * [Nidhogg] WAMP server driver
 *
 * @package Yggdrasil\Component\NidhoggComponent\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class WampServerDriver implements DriverInterface
{
    /**
     * Instance of server adapter
     *
     * @var WampServerAdapter
     */
    protected static $serverAdapterInstance;

    /**
     * Returns server adapter instance
     *
     * @param ConfigurationInterface $appConfiguration
     * @return WampServerAdapter
     *
     * @throws MissingConfigurationException if host, port or topic_namespace is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): WampServerAdapter
    {
        if (self::$serverAdapterInstance === null) {
            $requiredConfig = ['host', 'port', 'topic_namespace'];

            if (!$appConfiguration->isConfigured($requiredConfig, 'wamp_server')) {
                throw new MissingConfigurationException($requiredConfig, 'wamp_server');
            }

            self::$serverAdapterInstance = new WampServerAdapter(
                new WampServer(), new RouteCollector(), $appConfiguration
            );

        }

        return self::$serverAdapterInstance;
    }
}