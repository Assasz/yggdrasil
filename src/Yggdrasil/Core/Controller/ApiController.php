<?php

namespace Yggdrasil\Core\Controller;

use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Driver\Base\DriverCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 *
 * Base class for REST api controllers
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class ApiController
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
     * @param DriverCollection $drivers
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
     * Returns request body data specified by key
     *
     * @param string $key
     * @return mixed
     *
     * @throws \InvalidArgumentException if data specified by key doesn't exist
     */
    protected function fromBody(string $key)
    {
        if ($this->getRequest()->headers->get('Content-Type') === 'application/json') {
            $dataCollection = json_decode($this->getRequest()->getContent(), true);
        } else {
            if ($this->getRequest()->isMethod('POST')) {
                $dataCollection = array_merge(
                    $this->getRequest()->request->all(),
                    $this->getRequest()->files->all()
                );
            } else {
                parse_str($this->getRequest()->getContent(), $dataCollection);
            }
        }

        if (!isset($dataCollection[$key])) {
            throw new \InvalidArgumentException('Data with key ' . $key . ' doesn\'t exist in request body.');
        }

        return $dataCollection[$key];
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
     * Enables CORS in given action or controller
     *
     * @param array $options Set of CORS options (allow_origin, allow_methods, allow_headers, allow_credentials, max_age)
     */
    protected function enableCors(array $options = []): void
    {
        $corsConfig = [
            'Access-Control-Allow-Origin'
            => $options['allow_origin'] ?? '*',
            'Access-Control-Allow-Methods'
            => $options['allow_methods'] ?? 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'
            => $options['allow_headers'] ?? '*',
            'Access-Control-Allow-Credentials'
            => $options['allow_credentials'] ?? true,
            'Access-Control-Allow-Max-Age'
            => $options['max_age'] ?? 3600
        ];
  
        foreach ($corsConfig as $key => $value) {
            $this->getResponse()->headers->set($key, $value);
        }
    }
}
