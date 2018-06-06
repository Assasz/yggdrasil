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

        return (!$partial) ? $this->getResponse()->setContent($template): $template;
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
     * @param array $resources          Resources supposed to be returned
     * @param int   $serializationDepth Entity association depth to be pursued by serialization
     * @return JsonResponse
     */
    protected function json(array $resources = [], int $serializationDepth = 1): JsonResponse
    {
        $data = [];

        foreach ($resources as $rKey => $resource) {
            if (is_array($resource)) {
                foreach ($resource as $iKey => $item) {
                    if ($item instanceof SerializableEntityInterface) {
                        $data[$rKey][$iKey] = EntitySerializer::toArray([$item], $serializationDepth);
                    } else {
                        $data[$rKey][$iKey] = $item;
                    }
                }
            } elseif ($resource instanceof SerializableEntityInterface) {
                $data[$rKey] = EntitySerializer::toArray([$resource], $serializationDepth);
            } else {
                $data[$rKey] = $resource;
            }
        }

        $this->getResponse()->headers->set('Content-Type', 'application/json');
        $headers = $this->getResponse()->headers->all();

        return new JsonResponse($data, Response::HTTP_OK, $headers);
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