<?php

namespace Yggdrasil\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Exception\ActionNotFoundException;

class Kernel
{
    private $configuration;

    use DriverAccessorTrait;

    public function __construct(ConfigurationInterface $appConfiguration)
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

                if (!method_exists($route->getController(), $route->getAction())) {
                    throw new ActionNotFoundException($action. ' passive action is present in your configuration, but can\'t be found or is improperly configured.');
                }

                $controllerName = $route->getController();
                $controller = new $controllerName($this->drivers, $request, $response);
                $response = $controller->{$route->getAction()}();
            }
        }

        return $response;
    }

    private function executeAction(Request $request, Response $response)
    {
        $route = $this->getRouter()->getRoute($request);

        if(!method_exists($route->getController(), $route->getAction())){
            if(!DEBUG) {
                return new Response("Not found.", Response::HTTP_NOT_FOUND);
            }

            throw new ActionNotFoundException($route->getAction().' for '.$route->getController().' not found.');
        }

        $controllerName = $route->getController();
        $controller = new $controllerName($this->drivers, $request, $response);
        return call_user_func_array([$controller, $route->getAction()], $route->getActionParams());
    }
}