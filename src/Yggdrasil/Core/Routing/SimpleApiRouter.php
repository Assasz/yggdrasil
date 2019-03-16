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
     * SimpleApiRouter constructor.
     *
     * @param RoutingConfiguration $configuration
     * @param Request $request
     */
    public function __construct(RoutingConfiguration $configuration, Request $request)
    {
        $this->configuration = $configuration;
        $this->request = $request;
    }

    /**
     * Resolves route for requested action
     *
     * @return Route?
     */
    public function resolveRoute(): ?Route
    {
        $patterns = [
            'with_identifier' => '#^(?P<controller>[a-z]+)/(?P<id>[0-9]+)$#',
            'no_identifier' => '#^(?P<controller>[a-z]+)$#'
        ];

        $query = strtolower(rtrim($this->request->query->get('route'), '/'));

        switch(true) {
            case preg_match($patterns['with_identifier'], $query, $matches):
                $route = $this->getRouteForWithIdentifierPattern($matches);

                break;
            case preg_match($patterns['no_identifier'], $query, $matches):
                $route = $this->getRouteForNoIdentifierPattern($matches);

                break;
            default:
                return null;
        }

        return $route;
    }

    /**
     * Return route for 'with_identifier' query pattern
     *
     * @param array $matches Result of regular expression match
     * @return Route?
     */
    private function getRouteForWithIdentifierPattern(array $matches): ?Route
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
    private function getRouteForNoIdentifierPattern(array $matches): ?Route
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
