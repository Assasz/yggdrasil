<?php

namespace Yggdrasil\Core\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Driver\Base\DriverCollection;

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
     * Trait that provides common controllers features
     */
    use ControllerTrait;

    /**
     * Trait that provides access to drivers
     */
    use DriverAccessorTrait;

    /**
     * AbstractController constructor.
     *
     * @param DriverCollection $drivers Drivers passed by Kernel
     * @param Request $request
     * @param Response $response
     */
    public function __construct(DriverCollection $drivers, Request $request, Response $response)
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
}
