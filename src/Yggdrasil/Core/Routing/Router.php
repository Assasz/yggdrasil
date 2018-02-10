<?php

namespace Yggdrasil\Core\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class Router
 *
 * Finds routes for requested resources
 *
 * @package Yggdrasil\Core\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class Router
{
    /**
     * Set of default controller and action names
     *
     * @var array
     */
    private $defaults;

    /**
     * Namespace of controllers
     *
     * @var string
     */
    private $controllerNamespace;

    /**
     * Set of route parameters consisting of controller, action and action parameters values, if exists
     *
     * @var array
     */
    private $routeParams;

    /**
     * Router constructor.
     *
     * Initialises arrays of $defaults and $routeParams
     */
    public function __construct()
    {
        $this->defaults = [];
        $this->routeParams = [];
    }

    /**
     * Returns route for requested resource
     *
     * @param Request $request
     * @return Route
     */
    public function getRoute(Request $request): Route
    {
        $query = $request->query->get('route');
        $this->routeParams = explode('/', trim($query, '/'));

        $route = new Route();
        $route->setController($this->resolveController());
        $route->setAction($this->resolveAction());
        $route->setActionParams($this->resolveActionParams());

        return $route;
    }

    /**
     * Returns route for requested passive action
     *
     * @param string $alias Alias of passive action like Controller:action
     * @return Route
     */
    public function getPassiveActionRoute(string $alias): Route
    {
        $this->routeParams = explode(':', $alias);

        $route = new Route();
        $route->setController($this->resolveController());
        $route->setAction($this->resolvePassiveAction());

        return $route;
    }

    /**
     * Returns absolute path for requested action
     *
     * @param string $alias        Alias of action like Controller:action
     * @param array  $actionParams Set of action parameters
     * @return string
     */
    public function getQuery(string $alias, array $actionParams = []): string
    {
        $queryParams = explode(':', mb_strtolower($alias));

        foreach($actionParams as $param){
            $queryParams[] = $param;
        }

        $query = implode('/', $queryParams);

        return BASE_URL.$query;
    }

    /**
     * Sets default controller and action names
     *
     * @param array $defaults
     *
     * @throws \InvalidArgumentException if controller and action keys can't be found in passed array
     */
    public function setDefaults(array $defaults): void
    {
        if(!array_key_exists('controller', $defaults) && !array_key_exists('action', $defaults)){
            throw new \InvalidArgumentException('Keys controller and action need to be specified in array to configure default routing.');
        }

        $this->defaults = $defaults;
    }

    /**
     * Sets namespace of controllers
     *
     * @param string $namespace
     */
    public function setControllerNamespace(string $namespace): void
    {
        $this->controllerNamespace = $namespace;
    }

    /**
     * Resolves controller depending on route parameter, returns default value otherwise
     *
     * @return string
     */
    private function resolveController(): string
    {
        $controller = (!empty($this->routeParams[0])) ? $this->namespace.ucfirst($this->routeParams[0]).'Controller' : $this->namespace.$this->defaults['controller'];

        return $controller;
    }

    /**
     * Resolves action depending on route parameter, returns default value otherwise
     *
     * @return string
     */
    private function resolveAction(): string
    {
        $action = (!empty($this->routeParams[1])) ? $this->routeParams[1].'Action' : $this->defaults['action'];

        return $action;
    }

    /**
     * Resolves passive action depending on route parameter
     *
     * @return string
     */
    private function resolvePassiveAction(): string
    {
        $action = $this->routeParams[1].'PassiveAction';

        return $action;
    }

    /**
     * Resolves action parameters depending on route parameters, if exists
     *
     * @return array
     */
    private function resolveActionParams(): array
    {
        $actionParams = [];

        for($i = 2; $i <= count($this->routeParams) - 1; $i++){
            $actionParams[] = $this->routeParams[$i];
        }

        return $actionParams;
    }
}