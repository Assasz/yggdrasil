<?php

namespace Yggdrasil\Utils\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Yggdrasil\Core\Exception\InvalidCsrfTokenException;

/**
 * Class FormHandler
 *
 * Handles form submission
 *
 * @package Yggdrasil\Utils\Form
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class FormHandler
{
    /**
     * GET HTTP method
     *
     * @var string
     */
    private const HTTP_GET = 'GET';

    /**
     * POST HTTP method
     */
    private const HTTP_POST = 'POST';

    /**
     * Collection od form data
     *
     * @var array
     */
    private $dataCollection;

    /**
     * FormHandler constructor.
     *
     * Initialises array of $dataCollection
     */
    public function __construct()
    {
        $this->dataCollection = [];
    }

    /**
     * Returns result of form submission
     *
     * @param Request $request
     * @param string  $method Expected form HTTP method
     * @return bool
     *
     * @throws InvalidCsrfTokenException if received CSRF token doesn't match token stored in session
     */
    public function handle(Request $request, string $method = self::HTTP_POST): bool
    {
        if (!$request->isMethod($method)) {
            return false;
        }

        if ($request->request->has('csrf_token')) {
            $session = new Session();

            if ($session->get('csrf_token') !== $request->request->get('csrf_token')) {
                throw new InvalidCsrfTokenException();
            }

            $request->request->remove('csrf_token');
        }

        $this->dataCollection = array_merge($request->request->all(), $request->files->all());

        return true;
    }

    /**
     * Returns form data collection
     *
     * @return array
     */
    public function getDataCollection(): array
    {
        return $this->dataCollection;
    }

    /**
     * Returns given form data
     *
     * @param string $key Key of form data, equivalent to input name
     * @return mixed
     *
     * @throws \InvalidArgumentException if data can't be found
     */
    public function getData(string $key)
    {
        if (!$this->hasData($key)) {
            throw new \InvalidArgumentException($key . ' not found in submitted form data.');
        }

        return $this->dataCollection[$key];
    }

    /**
     * Checks if given form data exist in collection
     *
     * @param string $key Key of form data, equivalent to input name
     * @return bool
     */
    public function hasData(string $key): bool
    {
        return array_key_exists($key, $this->dataCollection);
    }
}
