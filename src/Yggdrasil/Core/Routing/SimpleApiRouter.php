<?php

namespace Yggdrasil\Core\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class SimpleApiRouter
 *
 * Finds route for requested action if simple API routing is enabled
 * If route cannot be resolved, responsibility is delegated back to Router
 *
 * @package Yggdrasil\Core\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class SimpleApiRouter
{
    /**
     * Routing configuration
     *
     * @var RoutingConfiguration
     */
    private $configuration;

    /**
     * Request obtained from Router
     *
     * @var Request
     */
    private $request;

    /**
     * Instance of router
     *
     * @var SimpleApiRouter
     */
    private static $instance;

    /**
     * SimpleApiRouter constructor.
     *
     * @param RoutingConfiguration $configuration
     * @param Request $request
     */
    private function __construct(RoutingConfiguration $configuration, Request $request)
    {
        $this->configuration = $configuration;
        $this->request = $request;
    }

    /**
     * Cloning disabled
     */
    private function __clone() { }

    /**
     * Returns instance of router
     *
     * @param RoutingConfiguration $configuration
     * @param Request $request
     * @return SimpleApiRouter
     */
    public static function getInstance(RoutingConfiguration $configuration, Request $request): SimpleApiRouter
    {
        if (null === self::$instance) {
            self::$instance = new SimpleApiRouter($configuration, $request);
        }

        return self::$instance;
    }

    /**
     * Detects route for requested action
     *
     * @return Route? If route cannot be resolved, NULL is returned
     */
    public function detectRoute(): ?Route
    {
        $patterns = [
            'with_identifier' => '#^(?P<controller>[a-z]+)/(?P<id>[0-9]+)$#',
            'no_identifier' => '#^(?P<controller>[a-z]+)$#'
        ];

        $query = strtolower(rtrim($this->request->query->get('route'), '/'));

        switch(true) {
            case preg_match($patterns['with_identifier'], $query, $matches):
                $route = $this->detectRouteForWithIdentifierPattern($matches);

                break;
            case preg_match($patterns['no_identifier'], $query, $matches):
                $route = $this->detectRouteForNoIdentifierPattern($matches);

                break;
            default:
                return null;
        }

        return $route;
    }

    /**
     * Returns route for 'with_identifier' query pattern
     *
     * @param array $matches Result of regular expression match
     * @return Route?
     */
    private function detectRouteForWithIdentifierPattern(array $matches): ?Route
    {
        $actions = [
            'GET' => 'singleAction',
            'PUT' => 'editAction',
            'DELETE' => 'destroyAction'
        ];

        if (!isset($actions[$this->request->getMethod()])) {
            return null;
        }

        $controller = $this->configuration->getControllerNamespace() . ucfirst($matches['controller']) . 'Controller';

        return (new Route())
            ->setController($controller)
            ->setAction($actions[$this->request->getMethod()])
            ->setActionParams([$matches['id']]);
    }

    /**
     * Returns route for 'no_identifier' query pattern
     *
     * @param array $matches Result of regular expression match
     * @return Route?
     */
    private function detectRouteForNoIdentifierPattern(array $matches): ?Route
    {
        $actions = [
            'GET' => 'allAction',
            'POST' => 'createAction'
        ];

        if (!isset($actions[$this->request->getMethod()])) {
            return null;
        }

        $controller = $this->configuration->getControllerNamespace() . ucfirst($matches['controller']) . 'Controller';

        return (new Route())
            ->setController($controller)
            ->setAction($actions[$this->request->getMethod()])
            ->setActionParams([]);
    }
}
