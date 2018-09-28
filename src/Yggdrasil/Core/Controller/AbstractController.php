<?php

namespace Yggdrasil\Core\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * Provides ability to manage request and response
     */
    use HttpManagerTrait;

    /**
     * Makes session management easy
     */
    use SessionManagerTrait;

    /**
     * Provides access to application drivers
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
     * Renders given view as response
     *
     * @param string $view    Name of view file
     * @param array  $params  Parameters supposed to be passed to the view
     * @return Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function render(string $view, array $params = []): Response
    {
        $this->getTemplateEngine()->addGlobal('_request', $this->getRequest());
        $template = $this->getTemplateEngine()->render($view, $params);

        return $this->getResponse()->setContent($template);
    }

    /**
     * Renders partial view
     *
     * @param string $view    Name of view file
     * @param array  $params  Parameters supposed to be passed to the view
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function renderPartial(string $view, array $params = []): string
    {
        $this->getTemplateEngine()->addGlobal('_request', $this->getRequest());

        return $this->getTemplateEngine()->render($view, $params);
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
