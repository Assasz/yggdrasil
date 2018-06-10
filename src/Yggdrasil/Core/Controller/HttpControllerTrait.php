<?php

namespace Yggdrasil\Core\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait HttpControllerTrait
 *
 * Trait that makes controller a HTTP port component
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
trait HttpControllerTrait
{
    /**
     * Request from client
     *
     * @var Request
     */
    private $request;

    /**
     * Response prepared to return
     *
     * @var Response
     */
    private $response;

    /**
     * Returns request
     *
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns response
     *
     * @return Response
     */
    protected function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Returns Bad Request (400) response
     *
     * @param string $message
     * @param bool $json Returns JsonResponse if true
     * @return Response|JsonResponse
     */
    protected function badRequest($message = 'Bad request.', bool $json = false)
    {
        if($json){
            $this->getResponse()->headers->set('Content-Type', 'application/json');
            $headers = $this->getResponse()->headers->all();

            return new JsonResponse($message, Response::HTTP_BAD_REQUEST, $headers);
        }

        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Returns Forbidden (403) response
     *
     * @param string $message
     * @param bool $json Returns JsonResponse if true
     * @return Response|JsonResponse
     */
    protected function accessDenied(string $message = 'Access denied.', bool $json = false)
    {
        if($json){
            $this->getResponse()->headers->set('Content-Type', 'application/json');
            $headers = $this->getResponse()->headers->all();

            return new JsonResponse($message, Response::HTTP_FORBIDDEN, $headers);
        }

        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_FORBIDDEN);
    }

    /**
     * Returns Not Found (404) response
     *
     * @param string $message
     * @param bool $json Returns JsonResponse if true
     * @return Response|JsonResponse
     */
    protected function notFound(string $message = 'Not found.', bool $json = false)
    {
        if($json){
            $this->getResponse()->headers->set('Content-Type', 'application/json');
            $headers = $this->getResponse()->headers->all();

            return new JsonResponse($message, Response::HTTP_NOT_FOUND, $headers);
        }

        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    /**
     * Returns Method Not Allowed (405) response
     *
     * @param string $message
     * @param bool $json Returns JsonResponse if true
     * @return Response|JsonResponse
     */
    protected function wrongMethod(string $message = "Wrong method.", bool $json = false)
    {
        if($json){
            $this->getResponse()->headers->set('Content-Type', 'application/json');
            $headers = $this->getResponse()->headers->all();

            return new JsonResponse($message, Response::HTTP_METHOD_NOT_ALLOWED, $headers);
        }

        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}