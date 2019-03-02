<?php

namespace Yggdrasil\Core\Routing;

use Symfony\Component\HttpFoundation\Request;

class SimpleApiRouter
{
    private $configuration;

    public function __construct(RoutingConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function resolveRoute(Request $request): ?Route
    {
        $query = $request->query->get('route');
        $routeParams = explode('/', trim($query, '/'));

        if (empty($routeParams)) {
            return null;
        }

        if (isset($routeParams[1]) && !is_numeric($routeParams[1])) {
            return null;
        }

        $controller = $this->configuration->getControllerNamespace() . $routeParams[0] . 'Controller';
        $action = 'allAction';
        $identifier = (isset($routeParams[1])) ? $routeParams[1] : null;

        switch ($request->getMethod()) {
            case 'GET':
                if (isset($routeParams[1])) {
                    $action = 'singleAction';
                }

                break;
            case 'POST':
                if (!isset($routeParams[1])) {
                    return null;
                }

                $action = 'createAction';

                break;
            case 'PUT':
                if (!isset($routeParams[1])) {
                    return null;
                }

                $action = 'editAction';

                break;
            case 'DELETE':
                if (!isset($routeParams[1])) {
                    return null;
                }

                $action = 'destroyAction';

                break;
            default:
                return null;
        }

        return (new Route())
            ->setController($controller)
            ->setAction($action)
            ->setActionParams((!empty($identifier)) ? [$identifier] : []);
    }
}