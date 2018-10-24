<?php

namespace Yggdrasil\Component\NidhoggComponent;

use Ratchet\App;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer as Wamp;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Yggdrasil\Component\NidhoggComponent\Exception\UnsupportedSessionProviderException;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\DriverNotFoundException;

/**
 * Class WampServer
 *
 * Hosts WebSocket application on WAMP spec
 *
 * @package Yggdrasil\Component\NidhoggComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class WampServer
{
    /**
     * Application configuration
     *
     * @var ConfigurationInterface
     */
    private $appConfiguration;

    /**
     * Collection of topic routes
     *
     * @var array
     */
    private $routes;

    /**
     * Sets application configuration
     *
     * @param ConfigurationInterface $appConfiguration
     * @return WampServer
     */
    public function setConfiguration(ConfigurationInterface $appConfiguration): WampServer
    {
        $this->appConfiguration = $appConfiguration;

        return $this;
    }

    /**
     * Sets socket routes
     *
     * @param array $routes
     * @return WampServer
     */
    public function setRoutes(array $routes): WampServer
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * Runs server
     *
     * @throws DriverNotFoundException if cache driver is not enabled for session provider
     * @throws UnsupportedSessionProviderException if enabled session provider is configured to not use Redis cache
     */
    public function run(): void
    {
        $configuration = $this->appConfiguration->getConfiguration();
        $hasSessionProvider = false;

        $server = new App($configuration['wamp_server']['host'], $configuration['wamp_server']['port']);

        if ($this->appConfiguration->isConfigured(['session_provider'], 'wamp_server')) {
            if (!$this->appConfiguration->hasDriver('cache')) {
                throw new DriverNotFoundException('Cache driver must be enabled to use session provider.');
            }

            $cache = $this->appConfiguration->loadDriver('cache');

            if (!$cache instanceof \Redis) {
                throw new UnsupportedSessionProviderException();
            }

            $hasSessionProvider = true;
        }

        foreach ($this->routes as $route) {
            if ($hasSessionProvider) {
                $session = new SessionProvider(
                    new WsServer(
                        new Wamp(
                            $route->getTopic()
                        )
                    ),
                    new RedisSessionHandler($cache)
                );
            }

            $server->route(
                $route->getPath(),
                ($hasSessionProvider) ? $session : $route->getTopic(),
                $route->getTopic()->getAllowedOrigins(),
                $route->getTopic()->getHost()
            );
        }

        $server->run();
    }
}