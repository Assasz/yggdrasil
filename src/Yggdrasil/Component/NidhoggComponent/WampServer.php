<?php

namespace Yggdrasil\Component\NidhoggComponent;

use Ratchet\App;

/**
 * Class WampServer
 *
 * Hosts WebSocket application on WAMP spec
 *
 * @package Yggdrasil\Component\NidhoggComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class WampServer
{
    /**
     * WampServer configuration
     *
     * @var array
     */
    private $configuration;

    /**
     * Collection of socket routes
     *
     * @var array
     */
    private $routes;

    /**
     * Runs server
     */
    public function run(): void
    {
        $server = new App($this->configuration['host'], $this->configuration['port']);

        foreach ($this->routes as $route => $socket) {
          $server->route($route, $socket);
        }

        $server->run();
    }

    /**
     * Sets WampServer configuration
     *
     * @param array $configuration
     * @return WampServer
     */
    public function setConfiguration(array $configuration): WampServer
    {
        $this->configuration = $configuration;

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
}