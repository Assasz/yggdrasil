<?php

namespace Yggdrasil\Component\NidhoggComponent\Driver;

use Yggdrasil\Component\NidhoggComponent\Routing\RouteCollector;
use Yggdrasil\Component\NidhoggComponent\WampServer;
use Yggdrasil\Component\NidhoggComponent\WampServerAdapter;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class WampServerDriver
 *
 * [Nidhogg] WAMP server driver
 *
 * @package Yggdrasil\Component\NidhoggComponent\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class WampServerDriver implements DriverInterface
{
    /**
     * Instance of driver
     *
     * @var DriverInterface
     */
    protected static $driverInstance;

    /**
     * Instance of server adapter
     *
     * @var WampServerAdapter
     */
    protected static $serverAdapterInstance;

    /**
     * Prevents object creation and cloning
     */
    private function __construct() {}

    private function __clone() {}

    /**
     * Returns server adapter instance
     *
     * @param ConfigurationInterface $appConfiguration
     * @return DriverInterface
     *
     * @throws MissingConfigurationException if host, port or topic_namespace is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): DriverInterface
    {
        if (self::$driverInstance === null) {
            $requiredConfig = ['host', 'port', 'topic_namespace'];

            if (!$appConfiguration->isConfigured($requiredConfig, 'wamp_server')) {
                throw new MissingConfigurationException($requiredConfig, 'wamp_server');
            }

            self::$serverAdapterInstance = new WampServerAdapter(
                new WampServer(), new RouteCollector(), $appConfiguration
            );

            self::$driverInstance = new WampServerDriver();
        }

        return self::$driverInstance;
    }

    /**
     * Runs WAMP server
     *
     * @throws \Exception
     */
    public function runServer()
    {
        self::$serverAdapterInstance->runServer();
    }
}