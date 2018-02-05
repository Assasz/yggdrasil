<?php

namespace Yggdrasil\Core\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class FormHandler
{
    private $dataCollection;

    public function __construct()
    {
        $this->dataCollection = [];
    }

    public function handle(Request $request)
    {
        $session = new Session();

        if($request->request->has('csrf_token')) {
            if ($session->get('csrf_token') !== $request->request->get('csrf_token')) {
                return false;
            }

            $request->request->remove('csrf_token');
        }

        if(!$request->isMethod('POST')){
            return false;
        }

        $this->dataCollection = $request->request->all();
        return true;
    }

    public function getDataCollection()
    {
        return $this->dataCollection;
    }

    public function getData($key)
    {
        return $this->dataCollection[$key];
    }

    public function hasData($key)
    {
        return array_key_exists($key, $this->dataCollection);
    }

    public function serializeData($object)
    {
        foreach ($this->dataCollection as $key => $value){
            $setter = 'set'.ucfirst(str_replace(['_', '-', '.'], '', $key));
            if(method_exists($object, $setter)){
                $object->{$setter}($value);
            }
        }

        return $object;
    }
}