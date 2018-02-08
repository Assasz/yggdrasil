<?php

namespace Yggdrasil\Core\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Driver\Base\DriverInstanceCollection;

abstract class AbstractController
{
    private $request;
    private $response;

    use DriverAccessorTrait;

    public function __construct(DriverInstanceCollection $drivers, Request $request, Response $response)
    {
        $this->drivers = $drivers;
        $this->request = $request;
        $this->response = $response;
    }

    protected function getRequest()
    {
        return $this->request;
    }

    protected function getResponse()
    {
        return $this->response;
    }

    protected function render($view, array $params = [])
    {
        $params['app']['request'] = $this->getRequest();
        $template = $this->getTemplateEngine()->render($view, $params);

        return $this->getResponse()->setContent($template);
    }

    protected function redirectToAction($alias, array $params = [])
    {
        $query = $this->getRouter()->getQuery($alias, $params);
        $headers = $this->getResponse()->headers->all();

        return new RedirectResponse($query, Response::HTTP_FOUND, $headers);
    }

    protected function isGranted()
    {
        $session = new Session();
        return $session->get('is_granted', false);
    }

    protected function accessDenied($message = 'Access denied.')
    {
        return $this->getResponse()->setContent($message)->setStatusCode(Response::HTTP_FORBIDDEN);
    }

    protected function notFound($message = 'Not found.')
    {
        return $this->getResponse()->setContent($message)->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    protected function json($data = [])
    {
        $headers = $this->getResponse()->headers->all();
        return new JsonResponse($data, Response::HTTP_OK, $headers);
    }
}