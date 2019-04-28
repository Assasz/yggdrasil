<?php

namespace Yggdrasil\Core\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RestRouter
 *
 * Finds route for requested action if REST routing is enabled
 * If route cannot be resolved, responsibility is delegated back to Router
 *
 * @package Yggdrasil\Core\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class RestRouter
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
     * @var RestRouter
     */
    private static $instance;

    /**
     * Pattern /{controller}/{id}
     *
     * @var string
     */
    private const WITH_IDENTIFIER_PATTERN = '#^(?P<controller>[a-z]+)/(?P<id>[0-9]+)$#';

    /**
     * Pattern /{controller}
     *
     * @var string
     */
    private const NO_IDENTIFIER_PATTERN = '#^(?P<controller>[a-z]+)$#';

    /**
     * RestRouter constructor.
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
     * @return RestRouter
     */
    public static function getInstance(RoutingConfiguration $configuration, Request $request): RestRouter
    {
        if (null === self::$instance) {
            self::$instance = new RestRouter($configuration, $request);
        }

        return self::$instance;
    }

    /**
     * Resolves route for requested action
     *
     * @return Route? If route cannot be resolved, NULL is returned
     */
    public function resolveRoute(): ?Route
    {
        $query = strtolower(rtrim($this->request->query->get('route'), '/'));

        switch(true) {
            case preg_match(self::WITH_IDENTIFIER_PATTERN, $query, $matches):
                $route = $this->detectRouteForWithIdentifierPattern($matches);

                break;
            case preg_match(self::NO_IDENTIFIER_PATTERN, $query, $matches):
                $route = $this->detectRouteForNoIdentifierPattern($matches);

                break;
            default:
                return null;
        }

        return $route;
    }

    /**
     * Returns route for WITH_IDENTIFIER query pattern
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

        $controllerName = implode('', array_map('ucfirst', explode('-', $matches['controller'])));
        $controller = $this->configuration->getControllerNamespace() . $controllerName . 'Controller';

        return (new Route())
            ->setController($controller)
            ->setAction($actions[$this->request->getMethod()])
            ->setActionParams([$matches['id']]);
    }

    /**
     * Returns route for NO_IDENTIFIER query pattern
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

        $controllerName = implode('', array_map('ucfirst', explode('-', $matches['controller'])));
        $controller = $this->configuration->getControllerNamespace() . $controllerName . 'Controller';

        return (new Route())
            ->setController($controller)
            ->setAction($actions[$this->request->getMethod()])
            ->setActionParams([]);
    }
}
