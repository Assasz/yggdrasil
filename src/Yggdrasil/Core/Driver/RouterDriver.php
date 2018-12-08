<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Routing\Route;
use Yggdrasil\Core\Routing\Router;
use Yggdrasil\Core\Routing\RoutingConfiguration;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RouterDriver
 *
 * Abstract router driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class RouterDriver
{
    /**
     * Instance of router
     *
     * @var Router
     */
    protected static $routerInstance;

    /**
     * Returns router instance
     *
     * @return Router
     */
    public function getComponentInstance(): Router
    {
        return self::$routerInstance;
    }

    /**
     * Returns route for requested action
     *
     * @param Request $request
     * @return Route
     */
    public function getRoute(Request $request): Route
    {
        return self::$routerInstance->getRoute($request);
    }

    /**
     * Returns route for requested action by alias
     *
     * @param string $alias  Alias of action like Controller:action:parameters where parameters are optional
     * @param array  $params Additional action parameters
     * @param int    $type   Type of action to find route for
     * @return Route
     */
    public function getAliasedRoute(string $alias, array $params = [], int $type = Router::ACTIVE_ACTION): Route
    {
        return self::$routerInstance->getAliasedRoute($alias, $params, $type);
    }

    /**
     * Returns absolute path for requested action
     *
     * @param string $alias  Alias of action like Controller:action
     * @param array  $params Set of action parameters
     * @return string
     */
    public function getQuery(string $alias, array $params = []): string
    {
        return self::$routerInstance->getQuery($alias, $params);
    }

    /**
     * Returns query map [Controller:action => query]
     *
     * @param array $protected Controllers to skip
     * @return array
     *
     * @throws \Exception
     */
    public function getQueryMap(array $protected = ['Error']): array
    {
        return self::$routerInstance->getQueryMap($protected);
    }

    /**
     * Returns alias of active action in lower case
     *
     * @param Request $request
     * @return string
     */
    public function getActionAlias(Request $request): string
    {
        return self::$routerInstance->getActionAlias($request);
    }

    /**
     * Returns routing configuration
     *
     * @return RoutingConfiguration
     */
    public function getConfiguration(): RoutingConfiguration
    {
        return self::$routerInstance->getConfiguration();
    }
}
