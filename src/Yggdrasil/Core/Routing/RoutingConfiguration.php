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
     * HTTP Not Found message
     *
     * @var string
     */
    private $notFoundMsg;

    /**
     * Collection of registered passive actions
     *
     * @var array
     */
    private $passiveActions;

    private $isSimpleApiRouting;

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

    /**
     * Returns HTTP Not Found message
     *
     * @return string
     */
    public function getNotFoundMsg(): string
    {
        return $this->notFoundMsg;
    }

    /**
     * Sets HTTP Not Found message
     *
     * @param string $msg
     * @return RoutingConfiguration
     */
    public function setNotFoundMsg(string $msg): RoutingConfiguration
    {
        $this->notFoundMsg = $msg;

        return $this;
    }

    /**
     * Returns passive actions
     *
     * @return array
     */
    public function getPassiveActions(): array
    {
        return $this->passiveActions['passive_actions'] ?? [];
    }

    /**
     * Sets passive actions
     *
     * @param array $passiveActions
     * @return RoutingConfiguration
     */
    public function setPassiveActions(array $passiveActions): RoutingConfiguration
    {
        $this->passiveActions = $passiveActions;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSimpleApiRouting()
    {
        return $this->isSimpleApiRouting ?? false;
    }

    /**
     * @return RoutingConfiguration
     */
    public function setSimpleApiRouting(): RoutingConfiguration
    {
        $this->isSimpleApiRouting = true;

        return $this;
    }
}
