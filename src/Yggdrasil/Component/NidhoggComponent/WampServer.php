<?php

namespace Yggdrasil\Component\NidhoggComponent;

use Ratchet\App;
use Yggdrasil\Core\Configuration\ConfigurationInterface;

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
     */
    public function run(): void
    {
        $configuration = $this->appConfiguration->getConfiguration();

        $server = new App($configuration['wamp']['host'], $configuration['wamp']['port']);

        foreach ($this->routes as $route) {
            $server->route(
                $route->getPath(),
                $route->getTopic(),
                $route->getTopic()->getAllowedOrigins(),
                $route->getTopic()->getHost()
            );
        }

        $server->run();
    }
}