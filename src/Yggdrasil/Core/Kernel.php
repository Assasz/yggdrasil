<?php

namespace Yggdrasil\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Exception\ActionNotFoundException;
use Yggdrasil\Core\Exception\WrongActionRequestedException;

/**
 * Class Kernel
 *
 * Heart of framework core, manages action execution
 *
 * @package Yggdrasil\Core
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class Kernel
{
    /**
     * Application configuration
     *
     * @var array
     */
    private $configuration;

    /**
     * Trait that provides access to drivers
     */
    use DriverAccessorTrait;

    /**
     * Kernel constructor.
     *
     * Gets application configuration and loads drivers
     *
     * @param ConfigurationInterface $appConfiguration
     */
    public function __construct(ConfigurationInterface $appConfiguration)
    {
        $this->configuration = $appConfiguration->getConfiguration();
        $this->drivers = $appConfiguration->loadDrivers();
    }

    /**
     * Handles request and returns response to the client
     *
     * @param Request $request
     * @return mixed|Response
     */
    public function handle(Request $request)
    {
        $response = new Response();

        $response = $this->executePassiveActions($request, $response);
        return $this->executeAction($request, $response);
    }

    /**
     * Executes passive actions and returns modified or not response
     *
     * @param Request  $request
     * @param Response $response New object of Response
     * @return Response
     *
     * @throws ActionNotFoundException if passive action can't be found, but exists in configuration
     */
    private function executePassiveActions(Request $request, Response $response): Response
    {
        if(array_key_exists('passive_action', $this->configuration)) {
            foreach ($this->configuration['passive_action'] as $action) {
                $route = $this->getRouter()->getAliasedRoute($action, [], true);

                if (!method_exists($route->getController(), $route->getAction())) {
                    throw new ActionNotFoundException($action. ' passive action is present in your configuration, but can\'t be found or is improperly configured.');
                }

                $controllerName = $route->getController();
                $controller = new $controllerName($this->drivers, $request, $response);
                $response = call_user_func_array([$controller, $route->getAction()], $route->getActionParams());
            }
        }

        return $response;
    }

    /**
     * Executes action and returns response
     *
     * @param Request  $request
     * @param Response $response Response returned by passive actions if exists, can be modified
     * @return mixed|Response
     *
     * @throws ActionNotFoundException if requested action can't be found, but only in debug mode
     * @throws WrongActionRequestedException if requested action is partial or passive
     */
    private function executeAction(Request $request, Response $response)
    {
        $route = $this->getRouter()->getRoute($request);

        if(!method_exists($route->getController(), $route->getAction())){
            if(!DEBUG) {
                return new Response($this->configuration['routing']['http_not_found_message'] ?? 'Not found.', Response::HTTP_NOT_FOUND);
            }

            throw new ActionNotFoundException($route->getAction().' for '.$route->getController().' not found.');
        }

        if(preg_match('(partial|passive)', strtolower($route->getAction())) === 1){
            if(!DEBUG) {
                return new Response('Forbidden.', Response::HTTP_FORBIDDEN);
            }

            throw new WrongActionRequestedException('Partial and passive actions cannot be requested by user.');
        }

        $controllerName = $route->getController();
        $controller = new $controllerName($this->drivers, $request, $response);
        return call_user_func_array([$controller, $route->getAction()], $route->getActionParams());
    }
}