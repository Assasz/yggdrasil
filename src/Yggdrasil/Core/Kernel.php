<?php

namespace Yggdrasil\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppModule\Infrastructure\Config\AppConfiguration;
use Yggdrasil\Core\Routing\Route;
use Yggdrasil\Core\Routing\Router;

class Kernel
{
    private $drivers;
    private $appConfiguration;

    public function __construct()
    {
        $this->appConfiguration = new AppConfiguration();
        $this->drivers = $this->appConfiguration->loadDrivers();
    }

    public function handle(Request $request)
    {
        $router = $this->drivers['router'];
        $route = $router->getRoute($request);

        $response = $this->executePassiveActions($this->getPassiveActionsRoutes(), $request);
        return $this->executeAction($route, $request, $response);
    }

    private function getPassiveActionsRoutes()
    {
        $configuration = $this->appConfiguration->getConfiguration();
        $router = $this->drivers['router'];
        $passiveActionsRoutes = [];

        foreach ($configuration['passive_action'] as $action){
            $passiveActionsRoutes[] = $router->getPassiveActionRoute($action);
        }

        return $passiveActionsRoutes;
    }

    private function executePassiveActions(array $routes, Request $request)
    {
        $response = new Response();

        foreach ($routes as $route){
            if(method_exists($route->getController(), $route->getAction())){
                $controllerName = $route->getController();
                $controller = new $controllerName($this->drivers, $request, $response);
                $response = $controller->{$route->getAction()}();
            }
        }

        return $response;
    }

    private function executeAction(Route $route, Request $request, Response $response)
    {
        if(method_exists($route->getController(), $route->getAction())){
            $controllerName = $route->getController();
            $controller = new $controllerName($this->drivers, $request, $response);
            return call_user_func_array([$controller, $route->getAction()], $route->getActionParams());
        }

        return new Response("No route found.", Response::HTTP_NOT_FOUND);
    }
}