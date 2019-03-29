<?php

namespace Yggdrasil\Core;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Controller\ErrorControllerInterface;
use Yggdrasil\Core\Driver\DriverAccessorTrait;
use Yggdrasil\Core\Exception\ActionForbiddenException;
use Yggdrasil\Core\Exception\ActionNotFoundException;
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
     * Application configuration
     *
     * @var array
     */
    private $configuration;

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
        $this->configuration = $appConfiguration->getConfiguration();

        if ($this->drivers->has('errorHandler')) {
            $this->drivers->get('errorHandler');
        }

        AnnotationRegistry::registerAutoloadNamespace("Yggdrasil\Utils\Annotation", dirname(__DIR__, 2));
    }

    /**
     * Handles request and returns response to the client
     *
     * @param Request $request
     * @return mixed|Response
     * @throws \Exception
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
     * @throws ActionNotFoundException if passive action can't be found
     * @throws \Exception
     */
    private function executePassiveActions(Request $request, Response $response): Response
    {
        foreach ($this->getRouter()->getConfiguration()->getPassiveActions() as $action => $whitelist) {
            $activeAction = $this->getRouter()->getActionAlias($request);

            if (!in_array($activeAction, $whitelist) && !in_array('all', $whitelist)) {
                continue;
            }

            if (in_array('-' . $activeAction, $whitelist) && in_array('all', $whitelist)) {
                continue;
            }

            $route = $this->getRouter()->getAliasedRoute($action, [], Router::PASSIVE_ACTION);

            if (!method_exists($route->getController(), $route->getAction())) {
                throw new ActionNotFoundException($action . ' passive action is present in registry, but can\'t be found or is improperly configured.');
            }

            $controllerName = $route->getController();
            $controller = new $controllerName($this->drivers, $request, $response);
            $response = $controller->{$route->getAction()}(...$route->getActionParams());
        }

        return $response;
    }

    /**
     * Executes active action
     *
     * @param Request  $request
     * @param Response $response Response returned by passive actions execution
     * @return mixed|Response
     * @throws ActionNotFoundException if requested action can't be found in dev mode
     * @throws ActionForbiddenException if requested action is partial, passive or belongs to ErrorController in dev mode
     */
    private function executeAction(Request $request, Response $response)
    {
        $route = $this->getRouter()->getRoute($request);

        if (!method_exists($route->getController(), $route->getAction())) {
            if ('prod' === $this->configuration['framework']['env']) {
                return $response
                    ->setContent($this->getRouter()->getConfiguration()->getNotFoundMsg() ?? 'Not found.')
                    ->setStatusCode(Response::HTTP_NOT_FOUND);
            }

            throw new ActionNotFoundException($route->getAction() . ' for ' . $route->getController() . ' not found.');
        }

        $errorController = $this->getRouter()->getConfiguration()->getControllerNamespace() . 'ErrorController';

        if (1 === preg_match('(Partial|Passive)', $route->getAction()) || $errorController === $route->getController()) {
            if ('prod' === $this->configuration['framework']['env']) {
                return $response
                    ->setContent('Forbidden.')
                    ->setStatusCode(Response::HTTP_FORBIDDEN);
            }

            throw new ActionForbiddenException('Partial, passive and error actions cannot be requested by user.');
        }

        $controllerName = $route->getController();
        $controller = new $controllerName($this->drivers, $request, $response);

        return $controller->{$route->getAction()}(...$route->getActionParams());
    }

    /**
     * Handles HTTP errors, that may occur on action execution stage
     *
     * @param Request  $request
     * @param Response $response Response returned by action execution
     * @return mixed|Response
     */
    private function handleError(Request $request, Response $response)
    {
        $controllerName = $this->getRouter()->getConfiguration()->getControllerNamespace() . 'ErrorController';

        if (!class_exists($controllerName)) {
            return $response;
        }

        $actionName = 'code' . $response->getStatusCode() . 'Action';

        if (!method_exists($controllerName, $actionName)) {
            $actionName = 'defaultAction';
        }

        $controller = new $controllerName($this->drivers, $request, $response);

        return $controller->{$actionName}();
    }
}
