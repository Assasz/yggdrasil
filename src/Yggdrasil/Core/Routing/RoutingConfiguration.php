<?php

namespace Yggdrasil\Core\Routing;

/**
 * Class RoutingConfiguration
 *
 * Provides configuration for routing
 *
 * @package Yggdrasil\Core\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class RoutingConfiguration
{
    /**
     * Base URL of application
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Controllers namespace
     *
     * @var string
     */
    private $controllerNamespace;

    /**
     * Default controller
     *
     * @var string
     */
    private $defaultController;

    /**
     * Default action
     *
     * @var string
     */
    private $defaultAction;

    /**
     * Returns base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Sets base URL
     *
     * @param string $baseUrl
     * @return RoutingConfiguration
     */
    public function setBaseUrl(string $baseUrl): RoutingConfiguration
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Returns controllers namespace
     *
     * @return string
     */
    public function getControllerNamespace(): string
    {
        return $this->controllerNamespace;
    }

    /**
     * Sets controller namespace
     *
     * @param string $controllerNamespace
     * @return RoutingConfiguration
     */
    public function setControllerNamespace(string $controllerNamespace): RoutingConfiguration
    {
        $this->controllerNamespace = $controllerNamespace;

        return $this;
    }

    /**
     * Returns default controller
     *
     * @return string
     */
    public function getDefaultController(): string
    {
        return $this->defaultController;
    }

    /**
     * Sets default controller
     *
     * @param string $defaultController
     * @return RoutingConfiguration
     */
    public function setDefaultController(string $defaultController): RoutingConfiguration
    {
        $this->defaultController = $defaultController;

        return $this;
    }

    /**
     * Returns default action
     *
     * @return string
     */
    public function getDefaultAction(): string
    {
        return $this->defaultAction;
    }

    /**
     * Sets default action
     *
     * @param string $defaultAction
     * @return RoutingConfiguration
     */
    public function setDefaultAction(string $defaultAction): RoutingConfiguration
    {
        $this->defaultAction = $defaultAction;

        return $this;
    }
}
