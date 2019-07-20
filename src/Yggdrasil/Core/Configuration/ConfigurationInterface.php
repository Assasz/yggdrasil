<?php

namespace Yggdrasil\Core\Configuration;

use Yggdrasil\Core\Driver\DriverCollection;
use Yggdrasil\Core\Driver\DriverInterface;
use Yggdrasil\Core\Exception\DriverNotFoundException;

/**
 * Interface ConfigurationInterface
 *
 * Provides connection between application configuration and framework core
 *
 * @package Yggdrasil\Core\Configuration
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface ConfigurationInterface
{
    /**
     * Returns collection of registered application drivers
     *
     * @return DriverCollection
     */
    public function loadDrivers(): DriverCollection;

    /**
     * Returns configured instance of given driver
     *
     * @param string $key Name of driver
     * @return DriverInterface
     * @throws DriverNotFoundException if given driver doesn't exist
     */
    public function installDriver(string $key): DriverInterface;

    /**
     * Checks if given driver exist in registry
     *
     * @param string $key Name of driver
     * @return bool
     */
    public function hasDriver(string $key): bool;

    /**
     * Returns configuration by key
     *
     * @param string $key     Configuration key
     * @param string $section Name of configuration file section
     * @return string
     */
    public function get(string $key, string $section): string;

    /**
     * Checks if given data exist in configuration
     *
     * @param array  $keys    Set of keys of data to check
     * @param string $section Name of configuration file section, in which given keys should exist
     * @return bool
     */
    public function isConfigured(array $keys, string $section): bool;
}
