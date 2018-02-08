<?php

namespace Yggdrasil\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppModule\Infrastructure\Config\AppConfiguration;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Routing\Router;

class Kernel
{
    private $configuration;

    use DriverAccessorTrait;

    public function __construct(AppConfiguration $appConfiguration)
    {
        $this->configuration = $appConfiguration->getConfiguration();
        $this->drivers = $appConfiguration->loadDrivers();
    }

    public function handle(Request $request)
    {
        $response = new Response();

        $response = $this->executePassiveActions($request, $response);
        return $this->executeAction($request, $response);
    }

    private function executePassiveActions(Request $request, Response $response)
    {
        if(array_key_exists('passive_action', $this->configuration)) {
            foreach ($this->configuration['passive_action'] as $action) {
                $route = $this->getRouter()->getPassiveActionRoute($action);

                if (method_exists($route->getController(), $route->getAction())) {
                    $controllerName = $route->getController();
                    $controller = new $controllerName($this->drivers, $request, $response);
                    $response = $controller->{$route->getAction()}();
                }
            }
        }

        return $response;
    }

    private function executeAction(Request $request, Response $response)
    {
        $route = $this->getRouter()->getRoute($request);

        if(method_exists($route->getController(), $route->getAction())){
            $controllerName = $route->getController();
            $controller = new $controllerName($this->drivers, $request, $response);
            return call_user_func_array([$controller, $route->getAction()], $route->getActionParams());
        }

        return new Response("Not found.", Response::HTTP_NOT_FOUND);
    }
}