<?php

namespace Yggdrasil\Core\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Driver\Base\DriverInstanceCollection;
use Yggdrasil\Component\DoctrineComponent\EntitySerializer;
use Yggdrasil\Component\DoctrineComponent\SerializableEntityInterface;

/**
 * Class AbstractController
 *
 * Base class for application controllers
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class AbstractController
{
    /**
     * Trait that makes controller a HTTP port component
     */
    use HttpControllerTrait;

    /**
     * Trait that provides access to drivers
     */
    use DriverAccessorTrait;

    /**
     * AbstractController constructor.
     *
     * @param DriverInstanceCollection $drivers Drivers passed by Kernel
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
     * Renders given view
     *
     * @param string $view    Name of view file
     * @param array  $params  Parameters supposed to be passed to the view
     * @param bool   $partial Indicates if rendered view is partial
     * @return Response|string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function render(string $view, array $params = [], bool $partial = false)
    {
        $this->getTemplateEngine()->addGlobal('_request', $this->getRequest());
        $template = $this->getTemplateEngine()->render($view, $params);

        return (!$partial) ? $this->getResponse()->setContent($template) : $template;
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
     * Returns JSON encoded response
     *
     * @param array  $data   Data supposed to be returned
     * @param string $status Response status code
     * @return JsonResponse
     */
    protected function json(array $data = [], string $status = Response::HTTP_OK): JsonResponse
    {
        $this->getResponse()->headers->set('Content-Type', 'application/json');
        $headers = $this->getResponse()->headers->all();

        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Adds flash to session flash bag
     *
     * @param string       $type    Type of flash bag
     * @param string|array $message Message of flash
     */
    protected function addFlash(string $type, $message): void
    {
        $session = new Session();
        $session->getFlashBag()->set($type, $message);
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
     * Returns authenticated user instance from session
     *
     * @return mixed
     */
    protected function getUser()
    {
        $session = new Session();

        if($this->isGranted()){
            return $session->get('user');
        }
    }
}