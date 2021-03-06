<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\DriverNotFoundException;
use Yggdrasil\Core\Exception\NotDriverProvidedException;

/**
 * Class DriverCollection
 *
 * Collection of application drivers
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class DriverCollection implements \Iterator, \Countable
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
    private $configuration;

    /**
     * DriverCollection constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->drivers = [];
        $this->configuration = $configuration;
    }

    /**
     * Returns all drivers in an array
     *
     * @return array
     */
    public function all(): array
    {
        return $this->drivers;
    }

    /**
     * Adds driver to collection
     *
     * @param string $key    Name of driver
     * @param string $driver Driver class name
     * @throws NotDriverProvidedException if given object is of another instance than DriverInterface
     */
    public function add(string $key, string $driver): void
    {
        if (!in_array(DriverInterface::class, class_implements($driver))) {
            throw new NotDriverProvidedException('Provided ' . $key . ' is not a valid driver.');
        }

        $this->drivers[$key] = $driver;
    }

    /**
     * Returns configured driver instance
     * This method in fact 'installs' driver in application
     *
     * @param string $key Name of driver
     * @return DriverInterface
     * @throws DriverNotFoundException if requested driver doesn't exist
     */
    public function get(string $key): DriverInterface
    {
        if (!$this->has($key)) {
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that ' . $key . ' driver is properly configured.');
        }

        return $this->drivers[$key]::install($this->configuration);
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
     * @throws DriverNotFoundException if requested driver doesn't exist
     */
    public function remove(string $key): void
    {
        if (!$this->has($key)) {
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that ' . $key . ' driver is properly configured.');
        }

        unset($this->drivers[$key]);
    }

    /**
     * Counts collection items
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->drivers);
    }

    /**
     * Returns current collection item
     *
     * @return string
     */
    public function current(): string
    {
        return current($this->drivers);
    }

    /**
     * Returns current collection index
     *
     * @return string
     */
    public function key(): string
    {
        return key($this->drivers);
    }

    /**
     * Increments collection index
     */
    public function next(): void
    {
        next($this->drivers);
    }

    /**
     * Rewinds collection
     */
    public function rewind(): void
    {
        reset($this->drivers);
    }

    /**
     * Validates current collection index
     *
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->drivers) !== null;
    }

    /**
     * Returns application configuration
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }
}
