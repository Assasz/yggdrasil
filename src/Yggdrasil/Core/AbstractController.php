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

    public function __construct(array $drivers, Request $request)
    {
        $this->drivers = $drivers;
        $this->request = $request;
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

    protected function render($view, array $params = [])
    {
        $params['app']['request'] = $this->getRequest();
        $template = $this->drivers['templateEngine']->render($view, $params);

        return new Response($template);
    }

    protected function redirectToAction($alias, array $params = [])
    {
        $router = $this->drivers['router'];
        $query = $router->getQuery($alias, $params);

        return new RedirectResponse($query);
    }

    protected function redirectWithCookie($alias, Cookie $cookie, array $params = [])
    {
        $router = $this->drivers['router'];
        $query = $router->getQuery($alias, $params);

        $response = new RedirectResponse($query);
        $response->headers->setCookie($cookie);

        return $response;
    }

    protected function isGranted()
    {
        $session = new Session();
        return $session->get('is_granted', false);
    }

    protected function accessDenied($message = 'Access denied.')
    {
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    protected function notFound($message = 'Not found.')
    {
        return new Response($message, Response::HTTP_NOT_FOUND);
    }
}