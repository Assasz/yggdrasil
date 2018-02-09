<?php

namespace Yggdrasil\Core\Driver\Base;

use Yggdrasil\Core\Exception\DriverNotFoundException;

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
            throw new \InvalidArgumentException($key.' driver that you want to add already exist.');
        }

        $this->instances[$key] = $instance;
    }

    public function get($key)
    {
        if(!$this->has($key)){
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that '.$key.' driver is configured.');
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
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that '.$key.' driver is configured.');
        }

        unset($this->instances[$key]);
    }
}