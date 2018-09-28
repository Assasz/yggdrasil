<?php

namespace Yggdrasil\Core\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait HttpManagerTrait
 *
 * Provides ability to manage request and response
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
trait HttpManagerTrait
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
     * @return Response
     */
    protected function badRequest(string $message = 'Bad request.'): Response
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Returns Forbidden (403) response
     *
     * @param string $message
     * @return Response
     */
    protected function forbidden(string $message = 'Forbidden.'): Response
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_FORBIDDEN);
    }

    /**
     * Returns Not Found (404) response
     *
     * @param string $message
     * @return Response
     */
    protected function notFound(string $message = 'Not found.'): Response
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    /**
     * Returns Method Not Allowed (405) response
     *
     * @param string $message
     * @return Response
     */
    protected function methodNotAllowed(string $message = "Method not allowed."): Response
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Returns JSON encoded response
     *
     * @param array  $data   Data supposed to be returned
     * @param string $status Response status code
     * @return JsonResponse
     */
    protected function json(array $data = [], string $status = Response::HTTP_OK): JsonResponse
    {
        $headers = $this->getResponse()->headers->all();

        return new JsonResponse($data, $status, $headers);
    }
}
