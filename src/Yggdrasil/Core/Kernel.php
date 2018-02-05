<?php

namespace Yggdrasil\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppModule\Infrastructure\Config\AppConfiguration;
use Yggdrasil\Core\Routing\Router;

class Kernel
{
    private $drivers;

    public function __construct()
    {
        $configuration = new AppConfiguration();
        $this->drivers = $configuration->loadDrivers();
    }

    public function handle(Request $request)
    {
        $router = $this->drivers['router'];
        $route = $router->getRoute($request);

        if(method_exists($route->getController(), $route->getAction())){
            $controllerName = $route->getController();
            $controller = new $controllerName($this->drivers, $request);
            return call_user_func_array([$controller, $route->getAction()], $route->getActionParams());
        }

        return new Response("No route found.", Response::HTTP_NOT_FOUND);
    }
}