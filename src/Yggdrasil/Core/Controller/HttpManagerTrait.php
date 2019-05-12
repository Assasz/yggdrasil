<?php

namespace Yggdrasil\Core\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yggdrasil\Utils\Annotation\CORS;
use Yggdrasil\Utils\Entity\EntityNormalizer;

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
     * Returns Unprocessable Entity (422) response
     *
     * @param string $message
     * @return Response
     */
    protected function unprocessableEntity(string $message = "Unprocessable entity."): Response
    {
        return $this->getResponse()
            ->setContent($message)
            ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
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

        $serializedData = [];

        foreach ($data as $key => $item) {
            if (empty($item)) {
                continue;
            }

            if (is_array($item) && is_object($item[0])) {

                $serializedData[$key] = EntityNormalizer::normalize($item);

                unset($data[$key]);
            }

            if (is_object($item)) {
                $serializedData[$key] = EntityNormalizer::normalize([$item])[0];

                unset($data[$key]);
            }
        }

        $serializedData = array_merge($serializedData, $data);

        return new JsonResponse($serializedData, $status, $headers);
    }

    /**
     * Checks if action is requested with Yjax
     *
     * @return bool
     */
    protected function isYjaxRequest(): bool
    {
        return $this->getRequest()->headers->has('X-YJAX');
    }

    /**
     * Configures CORS in controller if is enabled by annotation
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    protected function configureCorsIfEnabled(): void
    {
        $reflection = new \ReflectionClass($this);
        $reader = new AnnotationReader();

        $annotation = $reader->getClassAnnotation($reflection, CORS::class);

        if (empty($annotation)) {
            return;
        }

        $corsConfig = [
            'Access-Control-Allow-Origin' => $annotation->origins ?? '*',
            'Access-Control-Allow-Methods' => $annotation->methods ?? 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => $annotation->headers ?? '*',
            'Access-Control-Allow-Credentials' => $annotation->credentials ?? true,
            'Access-Control-Allow-Max-Age' => $annotation->maxAge ?? 3600
        ];

        foreach ($corsConfig as $key => $value) {
            $this->getResponse()->headers->set($key, $value);
        }
    }
}
