<?php

namespace Yggdrasil\Core;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Yggdrasil\Core\Routing\Router;

abstract class AbstractController
{
    protected $drivers;
    protected $request;
    protected $response;

    public function __construct(array $drivers, Request $request, Response $response)
    {
        $this->drivers = $drivers;
        $this->request = $request;
        $this->response = $response;
    }

    protected function getDriver($name)
    {
        return $this->drivers[$name];
    }

    protected function getEntityManager()
    {
        return $this->drivers['entityManager'];
    }

    protected function getContainer()
    {
        return $this->drivers['container'];
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
        $template = $this->drivers['templateEngine']->render($view, $params);

        return $this->getResponse()->setContent($template);
    }

    protected function redirectToAction($alias, array $params = [])
    {
        $router = $this->drivers['router'];
        $query = $router->getQuery($alias, $params);
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
}