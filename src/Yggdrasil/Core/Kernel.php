<?php

namespace Yggdrasil\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Exception\ActionNotFoundException;
use Yggdrasil\Core\Exception\ActionForbiddenException;
use Yggdrasil\Core\Routing\Router;

/**
 * Class Kernel
 *
 * Heart of Yggdrasil, manages action execution
 *
 * @package Yggdrasil\Core
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class Kernel
{
    /**
     * Provides access to application drivers
     */
    use DriverAccessorTrait;

    /**
     * Kernel constructor.
     *
     * Initializes application
     *
     * @param ConfigurationInterface $appConfiguration
     */
    public function __construct(ConfigurationInterface $appConfiguration)
    {
        $this->drivers = $appConfiguration->loadDrivers();

        if ($this->drivers->has('exceptionHandler')) {
            $this->getExceptionHandler();
        }
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
        $response = $this->executeAction($request, $response);

        if ($response->isClientError() || $response->isServerError()) {
            return $this->handleError($request, $response);
        }

        return $response;
    }

    /**
     * Executes passive actions existing in registry
     *
     * @param Request  $request
     * @param Response $response
     * @return Response
     *
     * @throws ActionNotFoundException if passive action can't be found
     */
    private function executePassiveActions(Request $request, Response $response): Response
    {
        foreach ($this->getRouter()->getConfiguration()->getPassiveActions() as $action) {
            $route = $this->getRouter()->getAliasedRoute($action, [], Router::PASSIVE_ACTION);

            if (!method_exists($route->getController(), $route->getAction())) {
                throw new ActionNotFoundException($action . ' passive action is present in registry, but can\'t be found or is improperly configured.');
            }

            $controllerName = $route->getController();
            $controller = new $controllerName($this->drivers, $request, $response);
            $response = call_user_func_array([$controller, $route->getAction()], $route->getActionParams());
        }

        return $response;
    }

    /**
     * Executes action
     *
     * @param Request  $request
     * @param Response $response Response returned by passive actions execution
     * @return mixed|Response
     *
     * @throws ActionNotFoundException if requested action can't be found, in debug mode
     * @throws ActionForbiddenException if requested action is partial, passive or belongs to ErrorController, in debug mode
     */
    private function executeAction(Request $request, Response $response)
    {
        $route = $this->getRouter()->getRoute($request);

        if (!method_exists($route->getController(), $route->getAction())) {
            if (!DEBUG) {
                return $response
                    ->setContent('Not found.')
                    ->setStatusCode(Response::HTTP_NOT_FOUND);
            }

            throw new ActionNotFoundException($route->getAction() . ' for ' . $route->getController() . ' not found.');
        }

        $errorController = $this->getRouter()->getConfiguration()->getControllerNamespace() . 'ErrorController';

        if (1 === preg_match('(Partial|Passive)', $route->getAction()) || $errorController === $route->getController()) {
            if (!DEBUG) {
                return $response
                    ->setContent('Forbidden.')
                    ->setStatusCode(Response::HTTP_FORBIDDEN);
            }

            throw new ActionForbiddenException('Partial, passive and error actions cannot be requested by user.');
        }

        $controllerName = $route->getController();
        $controller = new $controllerName($this->drivers, $request, $response);

        return call_user_func_array([$controller, $route->getAction()], $route->getActionParams());
    }

    /**
     * Handles HTTP errors
     *
     * @param Request  $request
     * @param Response $response Response returned by action execution
     * @return mixed|Response
     */
    private function handleError(Request $request, Response $response)
    {
        $controllerName = $this->getRouter()->getConfiguration()->getControllerNamespace() . 'ErrorController';
        $actionName = 'code' . $response->getStatusCode() . 'Action';

        if (!method_exists($controllerName, $actionName)) {
            $actionName = 'defaultAction';

            if (!method_exists($controllerName, $actionName)) {
                return $response;
            }
        }

        $controller = new $controllerName($this->drivers, $request, $response);

        return call_user_func([$controller, $actionName]);
    }
}
