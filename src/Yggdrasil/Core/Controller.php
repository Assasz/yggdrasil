<?php

namespace Yggdrasil\Core;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Yggdrasil\Core\Routing\Router;

abstract class Controller
{
    protected $drivers;
    protected $request;

    public function __construct(array $drivers, Request $request)
    {
        $this->drivers = $drivers;
        $this->request = $request;
    }

    protected function getEntityManager()
    {
        return $this->drivers['entityManager'];
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
        $router = new Router();
        $query = $router->getQuery($alias, $params);
        return new RedirectResponse($query);
    }

    protected function getContainer()
    {
        return $this->drivers['container'];
    }
}