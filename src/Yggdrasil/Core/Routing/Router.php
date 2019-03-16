<?php

namespace Yggdrasil\Core\Routing;

use HaydenPierce\ClassFinder\ClassFinder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Router
 *
 * Finds route for requested action
 * Note that router doesn't care if provided route points to existing action - it's kernel responsibility
 *
 * @package Yggdrasil\Core\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class Router
{
    /**
     * Passive action type
     *
     * @var int
     */
    public const PASSIVE_ACTION = 0;

    /**
     * Active action type
     *
     * @var int
     */
    public const ACTIVE_ACTION = 1;

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
        if ($this->configuration->isSimpleApiRouting()) {
            $simpleRoute = (new SimpleApiRouter($this->configuration, $request))->resolveRoute();

            if (!empty($simpleRoute)) {
                return $simpleRoute;
            }
        }

        $query = $request->query->get('route');
        $this->routeParams = explode('/', trim($query, '/'));

        $route = (new Route())
            ->setController($this->resolveController())
            ->setAction($this->resolveAction())
            ->setActionParams($this->resolveActionParams());

        return $route;
    }

    /**
     * Returns route for requested action by alias
     *
     * @param string $alias   Alias of action like Controller:action:parameters where parameters are optional
     * @param array  $params  Additional action parameters
     * @param int    $type    Type of action to find route for
     * @return Route
     */
    public function getAliasedRoute(string $alias, array $params = [], int $type = self::ACTIVE_ACTION): Route
    {
        $this->routeParams = explode(':', $alias);

        foreach ($params as $param) {
            $this->routeParams[] = $param;
        }

        $route = (new Route())
            ->setController($this->resolveController())
            ->setAction((self::PASSIVE_ACTION === $type) ?
                $this->resolvePassiveAction() :
                $this->resolveAction())
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

        if (empty($queryParams[2]) && $queryParams[1] === $this->configuration->getDefaultAction()) {
            unset($queryParams[1]);

            if (ucfirst($queryParams[0]) === $this->configuration->getDefaultController()) {
                unset($queryParams[0]);
            }
        }

        $query = implode('/', $queryParams);

        return $this->configuration->getBaseUrl() . $query;
    }

    /**
     * Returns query map [Controller:action => query]
     *
     * @param array $protected Controllers to exclude from query map
     * @return array
     * @throws \Exception
     */
    public function getQueryMap(array $protected = ['Error']): array
    {
        $queryMap = [];

        $controllers = ClassFinder::getClassesInNamespace(
            rtrim($this->configuration->getControllerNamespace(), '\\')
        );

        foreach ($controllers as $controller) {
            $controllerReflection = new \ReflectionClass($controller);
            $controllerAlias = str_replace('Controller', '', $controllerReflection->getShortName());

            if (in_array($controllerAlias, $protected)) {
                continue;
            }

            $actions = $controllerReflection->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($actions as $action) {
                if (1 === preg_match('(Partial|Passive|__construct)', $action->getName())) {
                    continue;
                }

                $actionAlias = str_replace('Action', '', $action->getName());
                $alias = $controllerAlias . ':' . $actionAlias;
                $queryMap[$alias] = $this->getQuery($alias);
            }
        }

        return $queryMap;
    }

    /**
     * Returns alias of active action
     *
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function getActionAlias(Request $request): string
    {
        $actionAlias = str_replace('/', ':', $request->query->get('route'));

        if (empty($actionAlias)) {
            return $this->getConfiguration()->getDefaultController() . ':' . $this->getConfiguration()->getDefaultAction();
        }

        if (!strpos($actionAlias, ':')) {
            return $actionAlias . ':' . $this->getConfiguration()->getDefaultAction();
        }

        $aliases = array_keys($this->getQueryMap([]));

        foreach ($aliases as $alias) {
            if (strtolower($alias) === strtolower($actionAlias)) {
                $actionAlias = $alias;

                break;
            }
        }

        return $actionAlias;
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

    /**
     * Resolves controller depending on route parameter
     *
     * @return string
     */
    private function resolveController(): string
    {
        $controller = (!empty($this->routeParams[0])) ?
            $this->configuration->getControllerNamespace() . ucfirst($this->routeParams[0]) :
            $this->configuration->getControllerNamespace() . $this->configuration->getDefaultController();

        return $controller . 'Controller';
    }

    /**
     * Resolves action depending on route parameter
     *
     * @return string
     */
    private function resolveAction(): string
    {
        $action = (!empty($this->routeParams[1])) ?
            $this->routeParams[1] :
            $this->configuration->getDefaultAction();

        return $action . 'Action';
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
}
