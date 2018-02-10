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
class Route
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
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
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
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
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
     */
    public function setActionParams(array $actionParams): void
    {
        $this->actionParams = $actionParams;
    }
}