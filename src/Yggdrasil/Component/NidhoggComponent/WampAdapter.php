<?php

namespace Yggdrasil\Component\NidhoggComponent;

use HaydenPierce\ClassFinder\ClassFinder;
use Yggdrasil\Core\Configuration\ConfigurationInterface;

/**
 * Class WampAdapter
 *
 * Adapter for WampServer
 *
 * @package Yggdrasil\Component\NidhoggComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class WampAdapter
{
    /**
     * Instance of WampServer
     *
     * @var WampServer
     */
    private $server;

    /**
     * Application configuration
     *
     * @var ConfigurationInterface
     */
    private $appConfiguration;

    /**
     * WampAdapter constructor.
     *
     * @param WampServer $server
     * @param ConfigurationInterface $appConfiguration
     */
    public function __construct(WampServer $server, ConfigurationInterface $appConfiguration)
    {
        $this->appConfiguration = $appConfiguration;
        $configuration = $this->appConfiguration->getConfiguration();

        $this->server = $server->setConfiguration($configuration['wamp']);
    }

  /**
     * Returns routes for sockets in given namespace [route => socket]
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getRoutes(): array
    {
        $configuration = $this->appConfiguration->getConfiguration();

        $sockets = ClassFinder::getClassesInNamespace(rtrim($configuration['wamp']['socket_namespace'], '\\'));

        foreach ($sockets as $socket) {
            $socketReflection = new \ReflectionClass($socket);
        }
    }
}