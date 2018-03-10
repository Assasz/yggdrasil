<?php

namespace Yggdrasil\Core\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Driver\Base\DriverInstanceCollection;

/**
 * Class AbstractController
 *
 * Base class for application controllers, provides some helper methods
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class AbstractController
{
    /**
     * Request from client
     *
     * @var Request
     */
    private $request;

    /**
     * Response prepared to return
     *
     * @var Response
     */
    private $response;

    /**
     * Trait that provides access to drivers
     */
    use DriverAccessorTrait;

    /**
     * AbstractController constructor.
     *
     * @param DriverInstanceCollection $drivers
     * @param Request $request
     * @param Response $response
     */
    public function __construct(DriverInstanceCollection $drivers, Request $request, Response $response)
    {
        $this->drivers = $drivers;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Returns request
     *
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns response
     *
     * @return Response
     */
    protected function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Renders given view
     *
     * @param string $view   Name of view file
     * @param array  $params Parameters supposed to be passed to the view
     * @return Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function render(string $view, array $params = []): Response
    {
        $templating = $this->getTemplateEngine();
        $templating->addGlobal('_request', $this->getRequest());
        $template = $templating->render($view, $params);

        return $this->getResponse()->setContent($template);
    }

    /**
     * Redirects to given action
     *
     * @param string $alias  Alias of action like Controller:action
     * @param array  $params Parameters supposed to be passed to the action
     * @return RedirectResponse
     */
    protected function redirectToAction(string $alias, array $params = []): RedirectResponse
    {
        $query = $this->getRouter()->getQuery($alias, $params);
        $headers = $this->getResponse()->headers->all();

        return new RedirectResponse($query, Response::HTTP_FOUND, $headers);
    }

    /**
     * Checks if user is authenticated
     *
     * @return bool
     */
    protected function isGranted(): bool
    {
        $session = new Session();
        return $session->get('is_granted', false);
    }

    /**
     * Returns Forbidden (403) response
     *
     * @param string $message
     * @return Response
     */
    protected function accessDenied(string $message = 'Access denied.'): Response
    {
        return $this->getResponse()->setContent($message)->setStatusCode(Response::HTTP_FORBIDDEN);
    }

    /**
     * Returns Not Found (404) response
     *
     * @param string $message
     * @return Response
     */
    protected function notFound(string $message = 'Not found.'): Response
    {
        return $this->getResponse()->setContent($message)->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    /**
     * Returns Json encoded response
     *
     * @param array $data Data supposed to be returned
     * @return JsonResponse
     */
    protected function json(array $data = []): JsonResponse
    {
        $headers = $this->getResponse()->headers->all();
        return new JsonResponse($data, Response::HTTP_OK, $headers);
    }
}