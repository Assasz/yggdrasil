<?php

namespace Yggdrasil\Core\Controller;

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
     * Returns Forbidden (403) response
     *
     * @param string $message
     * @return Response
     */
    protected function accessDenied(string $message = 'Access denied.'): Response
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
    protected function wrongMethod(string $message = "Wrong method."): Response
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}