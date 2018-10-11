<?php

namespace Yggdrasil\Core\Routing;

/**
 * Class Route
 *
 * Object representation of route
 *
 * @package Yggdrasil\Core\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class Route
{
    /**
     * Name of controller
     *
     * @var string
     */
    private $controller;

    /**
     * Name of action
     *
     * @var string
     */
    private $action;

    /**
     * Set of action parameters
     *
     * @var array
     */
    private $actionParams;

    /**
     * Indicates if api is called
     *
     * @var bool
     */
    private $isApiCall;

    /**
     * Route constructor.
     *
     * Sets default value to isApiCall flag
     */
    public function __construct()
    {
        $this->isApiCall = false;
    }

  /**
     * Returns controller name
     *
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Sets controller name
     *
     * @param string $controller
     * @return Route
     */
    public function setController(string $controller): Route
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Returns action name
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Sets action name
     *
     * @param string $action
     * @return Route
     */
    public function setAction(string $action): Route
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Returns action parameters
     *
     * @return array
     */
    public function getActionParams(): array
    {
        return $this->actionParams;
    }

    /**
     * Sets action parameters
     *
     * @param array $actionParams
     * @return Route
     */
    public function setActionParams(array $actionParams): Route
    {
        $this->actionParams = $actionParams;

        return $this;
    }

    /**
     * Checks if api is called
     *
     * @return bool
     */
    public function isApiCall(): bool
    {
        return $this->isApiCall;
    }

    /**
     * Sets isApiCall flag
     *
     * @param bool $isApiCall
     * @return Route
     */
    public function setApiCall(bool $isApiCall): Route
    {
        $this->isApiCall = $isApiCall;

        return $this;
    }
}
