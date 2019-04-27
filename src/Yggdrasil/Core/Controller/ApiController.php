<?php

namespace Yggdrasil\Core\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yggdrasil\Utils\Annotation\CORS;
use Yggdrasil\Core\Driver\DriverAccessorTrait;
use Yggdrasil\Core\Driver\DriverCollection;

/**
 * Class ApiController
 *
 * Base class for API controllers
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class ApiController
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
     * ApiController constructor.
     *
     * @param DriverCollection $drivers
     * @param Request $request
     * @param Response $response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function __construct(DriverCollection $drivers, Request $request, Response $response)
    {
        $this->drivers  = $drivers;
        $this->request  = $request;
        $this->response = $response;

        $this->installDriversIfEnabled();
        $this->configureCorsIfEnabled();
    }

    /**
     * Returns request body data specified by key
     *
     * @param string $key
     * @return mixed
     * @throws \InvalidArgumentException if data specified by key doesn't exist
     */
    protected function fromBody(string $key)
    {
        $dataCollection = $this->parseBody();

        if (!$this->inBody([$key])) {
            throw new \InvalidArgumentException('Data with key ' . $key . ' doesn\'t exist in request body.');
        }

        return $dataCollection[$key];
    }

    /**
     * Checks if data with given keys exist in request body
     *
     * @param array $keys
     * @return bool
     */
    protected function inBody(array $keys): bool
    {
        return !array_diff_key(array_flip($keys), $this->parseBody());
    }

    /**
     * Parses request body into array
     *
     * @return array
     */
    protected function parseBody(): array
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

        return $dataCollection;
    }

    /**
     * Renders partial view
     *
     * @param string $view    Name of view file
     * @param array  $params  Parameters supposed to be passed to the view
     * @return string
     */
    protected function renderPartial(string $view, array $params = []): string
    {
        $this->getTemplateEngine()->addGlobal('_request', $this->getRequest());

        return $this->getTemplateEngine()->render($view, $params);
    }
}
