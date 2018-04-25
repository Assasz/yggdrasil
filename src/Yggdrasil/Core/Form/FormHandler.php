<?php

namespace Yggdrasil\Core\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Yggdrasil\Core\Exception\InvalidCsrfTokenException;
use Yggdrasil\Core\Service\ServiceRequestInterface;

/**
 * Class FormHandler
 *
 * Handles form submission, in current state rather helper class
 *
 * @package Yggdrasil\Core\Form
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class FormHandler
{
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
     * @return bool
     *
     * @throws InvalidCsrfTokenException if received CSRF token doesn't match token stored in session
     */
    public function handle(Request $request): bool
    {
        if(!$request->isMethod('POST')){
            return false;
        }

        if($request->request->has('csrf_token')) {
            $session = new Session();

            if(!$session->has('csrf_token')){
                return false;
            }

            if($session->get('csrf_token') !== $request->request->get('csrf_token')) {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }

            $request->request->remove('csrf_token');
        }

        $dataCollection = array_merge($request->request->all(), $request->files->all());
        $this->dataCollection = $dataCollection;

        return true;
    }

    /**
     * Returns all form data collection
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
        if(!$this->hasData($key)){
            throw new \InvalidArgumentException($key.' not found in form data, that you submitted.');
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

    /**
     * Helper method that serializes form data into service request object
     * Can be useful only in particular cases
     *
     * @param ServiceRequestInterface $request
     * @return ServiceRequestInterface
     */
    public function serializeData(ServiceRequestInterface $request): ServiceRequestInterface
    {
        foreach ($this->dataCollection as $key => $value){
            $setter = 'set'.ucfirst(str_replace(['_', '-', '.'], '', $key));
            if(method_exists($request, $setter)){
                $request->{$setter}($value);
            }
        }

        return $request;
    }
}