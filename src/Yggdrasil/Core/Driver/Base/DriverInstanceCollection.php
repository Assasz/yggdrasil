<?php

namespace Yggdrasil\Core\Driver\Base;

class DriverInstanceCollection
{
    private $instances;

    public function __construct()
    {
        $this->instances = [];
    }

    public function add($key, $instance)
    {
        if($this->has($key)){
            //exception
        }

        $this->instances[$key] = $instance;
    }

    public function get($key)
    {
        if(!$this->has($key)){
            //exception
        }

        return $this->instances[$key];
    }

    public function has($key)
    {
        return array_key_exists($key, $this->instances);
    }

    public function remove($key)
    {
        if(!$this->has($key)){
            //exception
        }

        unset($this->instances[$key]);
    }
}