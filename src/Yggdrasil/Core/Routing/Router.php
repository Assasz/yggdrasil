<?php

namespace Yggdrasil\Core\Routing;

use HaydenPierce\ClassFinder\ClassFinder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Router
 *
 * Finds route for requested action
 *
 * @package Yggdrasil\Core\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class Router
{
    /**
     * Routing configuration
     *
     * @var RoutingConfiguration
     */
    private $configuration;

    /**
     * Set of route parameters consisting of controller, action and action parameters
     *
     * @var array
     */
    private $routeParams;

    /**
     * Router constructor.
     *
     * @param RoutingConfiguration $configuration
     */
    public function __construct(RoutingConfiguration $configuration)
    {
        $this->configuration = $configuration;
        $this->routeParams = [];
    }

    /**
     * Returns route for requested action
     *
     * @param Request $request
     * @return Route
     */
    public function getRoute(Request $request): Route
    {
        $query = $request->query->get('route');
        $this->routeParams = explode('/', trim($query, '/'));

        $isApiCall = false;

        if ($this->routeParams[0] === 'api') {
            unset($this->routeParams[0]);
            $this->routeParams = array_values($this->routeParams);

            $isApiCall = true;
        }

        $route = (new Route())
            ->setController($this->resolveController())
            ->setAction(($isApiCall) ?
                $this->resolveApiAction($request->getMethod()) :
                $this->resolveAction())
            ->setActionParams($this->resolveActionParams());

        return $route;
    }

    /**
     * Returns route for requested action by alias, useful for passive actions
     *
     * @param string $alias   Alias of action like Controller:action:parameters where parameters are optional
     * @param array  $params  Additional action parameters
     * @param bool   $passive Indicates if requested action is passive
     * @return Route
     */
    public function getAliasedRoute(string $alias, array $params = [], bool $passive = false): Route
    {
        $this->routeParams = explode(':', $alias);

        foreach ($params as $param) {
            $this->routeParams[] = $param;
        }

        $route = (new Route())
            ->setController($this->resolveController())
            ->setAction(($passive) ? $this->resolvePassiveAction() : $this->resolveAction())
            ->setActionParams($this->resolveActionParams());

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

        foreach ($actionParams as $param) {
            $queryParams[] = $param;
        }

        if (empty($queryParams[2]) && $queryParams[1] . 'Action' === $this->configuration->getDefaultAction()) {
            unset($queryParams[1]);

            if (ucfirst($queryParams[0]) . 'Controller' === $this->configuration->getDefaultController()) {
                unset($queryParams[0]);
            }
        }

        $query = implode('/', $queryParams);

        return $this->configuration->getBaseUrl() . $query;
    }

    /**
     * Returns query map like [Controller:action => query]
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getQueryMap(): array
    {
        $queryMap = [];

        $controllers = ClassFinder::getClassesInNamespace(
            rtrim($this->configuration->getControllerNamespace(), '\\')
        );

        foreach ($controllers as $controller) {
            $controllerReflection = new \ReflectionClass($controller);
            $controllerAlias = str_replace('Controller', '', $controllerReflection->getShortName());

            $actions = $controllerReflection->getMethods();

            foreach ($actions as $action) {
                if (1 === preg_match('(Partial|Passive)', $action->getName())) {
                    continue;
                }

                $actionAlias = str_replace(
                    ['Get', 'Post', 'Put', 'Delete', 'Action'],
                    '',
                    $action->getName()
                );

                $alias = $controllerAlias . ':' . $actionAlias;
                $queryMap[$alias] = $this->getQuery($alias);
            }
        }

        return $queryMap;
    }

    /**
     * Resolves controller depending on route parameter
     *
     * @return string
     */
    private function resolveController(): string
    {
        $controller = (!empty($this->routeParams[0])) ?
            $this->configuration->getControllerNamespace() . ucfirst($this->routeParams[0]) . 'Controller' :
            $this->configuration->getControllerNamespace() . $this->configuration->getDefaultController();

        return $controller;
    }

    /**
     * Resolves action depending on route parameter
     *
     * @return string
     */
    private function resolveAction(): string
    {
        $action = (!empty($this->routeParams[1])) ?
            $this->routeParams[1] . 'Action' :
            $this->configuration->getDefaultAction();

        return $action;
    }

    /**
     * Resolves passive action depending on route parameter
     *
     * @return string
     */
    private function resolvePassiveAction(): string
    {
        $action = $this->routeParams[1] . 'PassiveAction';

        return $action;
    }

    /**
     * Resolves api action depending on route parameter and HTTP method
     *
     * @param string $method HTTP method
     * @return string
     */
    private function resolveApiAction(string $method): string
    {
        $method = ucfirst(strtolower($method));

        $action = (!empty($this->routeParams[1])) ?
            $this->routeParams[1] . $method . 'Action' :
            $this->configuration->getDefaultAction();

        return $action;
    }

    /**
     * Resolves action parameters depending on route parameters
     *
     * @return array
     */
    private function resolveActionParams(): array
    {
        $actionParams = [];

        for ($i = 2; $i < count($this->routeParams); $i++) {
            $actionParams[] = $this->routeParams[$i];
        }

        return $actionParams;
    }

    /**
     * Returns routing configuration
     *
     * @return RoutingConfiguration
     */
    public function getConfiguration(): RoutingConfiguration
    {
        return $this->configuration;
    }
}
