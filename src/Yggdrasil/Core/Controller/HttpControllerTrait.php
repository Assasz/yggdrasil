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
     * @return Response|JsonResponse
     */
    protected function badRequest($message = 'Bad request.')
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Returns Forbidden (403) response
     *
     * @param string $message
     * @return Response|JsonResponse
     */
    protected function forbidden(string $message = 'Forbidden.')
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_FORBIDDEN);
    }

    /**
     * Returns Not Found (404) response
     *
     * @param string $message
     * @return Response|JsonResponse
     */
    protected function notFound(string $message = 'Not found.')
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    /**
     * Returns Method Not Allowed (405) response
     *
     * @param string $message
     * @return Response|JsonResponse
     */
    protected function methodNotAllowed(string $message = "Method not allowed.")
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}