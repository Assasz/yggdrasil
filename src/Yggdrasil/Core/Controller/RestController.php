<?php

namespace Yggdrasil\Core\Controller;

use Yggdrasil\Component\DoctrineComponent\EntitySerializer;
use Yggdrasil\Component\DoctrineComponent\SerializableEntityInterface;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Driver\Base\DriverInstanceCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class RestController
 *
 * Base class for REST api controllers
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class RestController
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
     * Enables CORS with specified headers
     *
     * @param array $origins
     * @param array $methods
     * @param array $headers
     * @param bool  $credentials
     * @param int   $maxAge
     */
    protected function enableCors(array $origins = ['*'], array $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], array $headers = ['*'], bool $credentials = true, int $maxAge = 3600): void
    {
        $origins = implode(', ', $origins);
        $methods = implode(', ', $methods);
        $headers = implode(', ', $headers);

        $this->getResponse()->headers->set('Access-Control-Allow-Origin', $origins);
        $this->getResponse()->headers->set('Access-Control-Allow-Methods', $methods);
        $this->getResponse()->headers->set('Access-Control-Allow-Headers', $headers);
        $this->getResponse()->headers->set('Access-Control-Allow-Credentials', $credentials);
        $this->getResponse()->headers->set('Access-Control-Max-Age', $maxAge);
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
        if($this->getRequest()->isMethod('POST')){
            $dataCollection = array_merge($this->getRequest()->request->all(), $this->getRequest()->files->all());
        } else {
            parse_str(file_get_contents('php://input'), $dataCollection);
        }

        if(empty($dataCollection[$key])){
            throw new \InvalidArgumentException('Data with key ' . $key . ' doesn\'t exist.');
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
    protected function renderPartial(string $view, array $params = [])
    {
        $this->getTemplateEngine()->addGlobal('_request', $this->getRequest());

        return $this->getTemplateEngine()->render($view, $params);
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
}