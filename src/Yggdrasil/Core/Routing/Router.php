<?php

namespace Yggdrasil\Core\Routing;

use Symfony\Component\HttpFoundation\Request;

class Router
{
    private $defaults;
    private $routeParams;

    public function __construct()
    {
        $this->defaults = [];
        $this->routeParams = [];
    }

    public function getRoute(Request $request)
    {
        $query = $request->query->get('route');
        $this->routeParams = explode('/', trim($query, '/'));

        $route = new Route();
        $route->setController($this->resolveController());
        $route->setAction($this->resolveAction());
        $route->setActionParams($this->resolveActionParams());

        return $route;
    }

    public function getQuery($alias, array $actionParams = [])
    {
        $queryParams = explode(':', mb_strtolower($alias));

        foreach($actionParams as $param){
            $queryParams[] = $param;
        }

        $query = implode('/', $queryParams);

        return BASE_URL.$query;
    }

    public function setDefaults(array $defaults)
    {
        if(array_key_exists('controller', $defaults) && array_key_exists('action', $defaults)) {
            $this->defaults = $defaults;
        }
    }

    public function resolveController()
    {
        $namespace = '\AppModule\Ports\Controller\\';
        $controller = (!empty($this->routeParams[0])) ? $namespace.ucfirst($this->routeParams[0]).'Controller' : $namespace.$this->defaults['controller'];

        return $controller;
    }

    public function resolveAction()
    {
        $action = (!empty($this->routeParams[1])) ? $this->routeParams[1].'Action' : $this->defaults['action'];

        return $action;
    }

    public function resolveActionParams()
    {
        $actionParams = [];

        for($i = 2; $i <= count($this->routeParams) - 1; $i++){
            $actionParams[] = $this->routeParams[$i];
        }

        return $actionParams;
    }
}