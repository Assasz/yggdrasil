<?php

namespace Yggdrasil\Component\NidhoggComponent;

use Yggdrasil\Core\Configuration\ConfigurationInterface;

/**
 * Class WampServerAdapter
 *
 * Adapter for WampServer
 *
 * @package Yggdrasil\Component\NidhoggComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class WampServerAdapter
{
    /**
     * Instance of WampServer
     *
     * @var WampServer
     */
    private $server;

    /**
     * Instance of RouteCollector
     *
     * @var RouteCollector
     */
    private $collector;

    /**
     * Application configuration
     *
     * @var ConfigurationInterface
     */
    private $appConfiguration;

    /**
     * WampServerAdapter constructor.
     *
     * @param WampServer $server
     * @param RouteCollector $collector
     * @param ConfigurationInterface $appConfiguration
     */
    public function __construct(WampServer $server, RouteCollector $collector, ConfigurationInterface $appConfiguration)
    {
        $this->server = $server;
        $this->collector = $collector;
        $this->appConfiguration = $appConfiguration;
    }

    /**
     * Runs configured WampServer
     *
     * @throws \Exception
     */
    public function runServer(): void
    {
        $configuration = $this->appConfiguration->getConfiguration();

        $routes = $this->collector
            ->setConfiguration($this->appConfiguration)
            ->getRouteCollection();

        $this->server
            ->setConfiguration($configuration['wamp'])
            ->setRoutes($routes)
            ->run();
    }
}