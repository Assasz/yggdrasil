<?php

namespace Yggdrasil\Core\Driver\Base;

use Yggdrasil\Core\Exception\DriverNotFoundException;

/**
 * Class DriverInstanceCollection
 *
 * Collection of objects instances returned by drivers (not drivers itself), so it keeps various types of objects
 * For convenience purpose let's just call it drivers instances
 *
 * @package Yggdrasil\Core\Driver\Base
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class DriverInstanceCollection
{
    /**
     * Drivers instances
     *
     * @var array
     */
    private $instances;

    /**
     * DriverInstanceCollection constructor.
     *
     * Initialises array of $instances
     */
    public function __construct()
    {
        $this->instances = [];
    }

    /**
     * Adds instance to collection
     *
     * @param string $key      Name of driver
     * @param mixed  $instance Instance of driver
     *
     * @throws \InvalidArgumentException if given driver already exist
     */
    public function add(string $key, $instance): void
    {
        if($this->has($key)){
            throw new \InvalidArgumentException($key.' driver that you want to add already exist.');
        }

        $this->instances[$key] = $instance;
    }

    /**
     * Returns instance of given driver
     *
     * @param string $key Name of driver
     * @return mixed
     *
     * @throws DriverNotFoundException if requested driver doesn't exist
     */
    public function get(string $key)
    {
        if(!$this->has($key)){
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that '.$key.' driver is properly configured.');
        }

        return $this->instances[$key];
    }

    /**
     * Checks if driver already exist in collection
     *
     * @param string $key Name of driver
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->instances);
    }

    /**
     * Removes given driver from collection
     * Generally driver should be removed in AppConfiguration of your application, but it may be used in particular cases
     *
     * @param string $key Name of driver
     *
     * @throws DriverNotFoundException if requested driver doesn't exist
     */
    public function remove(string $key): void
    {
        if(!$this->has($key)){
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that '.$key.' driver is properly configured.');
        }

        unset($this->instances[$key]);
    }
}