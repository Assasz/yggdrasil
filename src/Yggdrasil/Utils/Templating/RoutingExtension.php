<?php

namespace Yggdrasil\Utils\Templating;

use Symfony\Component\HttpFoundation\Request;
use Yggdrasil\Core\Driver\RouterDriver;
use Yggdrasil\Core\Routing\Route;

/**
 * Class RoutingExtension
 *
 * Provides routing extension for Twig
 *
 * @package Yggdrasil\Utils\Templating
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class RoutingExtension extends \Twig_Extension
{
    /**
     * Router instance
     *
     * @var RouterDriver
     */
    private $router;

    /**
     * RoutingExtension constructor.
     *
     * @param RouterDriver $router
     */
    public function __construct(RouterDriver $router)
    {
        $this->router = $router;
    }

    /**
     * Returns set of functions
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('path',      [$this, 'getPath']),
            new \Twig_Function('asset',     [$this, 'getAsset']),
            new \Twig_Function('route',     [$this, 'getRoute']),
            new \Twig_Function('query_map', [$this, 'getQueryMap'])
        ];
    }

    /**
     * Returns absolute path for requested action
     *
     * @param string? $alias  Alias of action like Controller:action, if left empty default action will be chosen
     * @param array   $params Set of action parameters
     * @return string
     */
    public function getPath(string $alias = null, array $params = []): string
    {
        if (empty($alias)) {
            $routerConfig = $this->router->getConfiguration();
            $alias = "{$routerConfig->getDefaultController()}:{$routerConfig->getDefaultAction()}";
        }

        return $this->router->getQuery($alias, $params);
    }

    /**
     * Returns absolute path for requested asset like CSS file
     *
     * @param string $path Path of asset relative to web directory
     * @return string
     */
    public function getAsset(string $path): string
    {
        return $this->router->getConfiguration()->getBaseUrl() . ltrim($path, '/');
    }

    /**
     * Returns route for given request
     *
     * @param Request $request
     * @return Route
     */
    public function getRoute(Request $request): Route
    {
        return $this->router->getRoute($request);
    }

    /**
     * Returns query map [Controller:action => query]
     *
     * @param array $protected Controllers to exclude from query map
     * @return array
     *
     * @throws \Exception
     */
    public function getQueryMap(array $protected = ['Error']): array
    {
        return $this->router->getQueryMap($protected);
    }
}
