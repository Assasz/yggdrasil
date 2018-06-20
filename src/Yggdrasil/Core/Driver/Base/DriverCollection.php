<?php

namespace Yggdrasil\Core\Driver\Base;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\DriverNotFoundException;

/**
 * Class DriverCollection
 *
 * Collection of application drivers
 *
 * @package Yggdrasil\Core\Driver\Base
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class DriverCollection
{
    /**
     * Application drivers
     *
     * @var array
     */
    private $drivers;

    /**
     * Configuration needed to configure drivers
     *
     * @var ConfigurationInterface
     */
    private $appConfiguration;

    /**
     * DriverCollection constructor.
     *
     * @param ConfigurationInterface $appConfiguration
     */
    public function __construct(ConfigurationInterface $appConfiguration)
    {
        $this->drivers = [];
        $this->appConfiguration = $appConfiguration;
    }

    /**
     * Adds driver to collection
     *
     * @param string $key Name of driver
     * @param string $driver
     *
     * @throws \LogicException if given driver doesn't implement DriverInterface
     */
    public function add(string $key, string $driver): void
    {
        if (!in_array(DriverInterface::class, class_implements($driver))) {
            throw new \LogicException($key . ' driver needs to implement DriverInterface.');
        }

        $this->drivers[$key] = $driver;
    }

    /**
     * Returns component instance of given driver
     *
     * @param string $key Name of driver
     * @return mixed
     *
     * @throws DriverNotFoundException if requested driver doesn't exist
     */
    public function get(string $key)
    {
        if (!$this->has($key)) {
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that ' . $key . ' driver is properly configured.');
        }

        return $this->drivers[$key]::getInstace($this->appConfiguration);
    }

    /**
     * Checks if given driver already exist in collection
     *
     * @param string $key Name of driver
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->drivers);
    }

    /**
     * Removes given driver from collection
     * Better way to do that is removing driver from AppConfiguration registry, but it was easy to implement
     *
     * @param string $key Name of driver
     *
     * @throws DriverNotFoundException if requested driver doesn't exist
     */
    public function remove(string $key): void
    {
        if (!$this->has($key)) {
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that '.$key.' driver is properly configured.');
        }

        unset($this->drivers[$key]);
    }
}