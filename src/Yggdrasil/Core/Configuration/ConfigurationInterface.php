<?php

namespace Yggdrasil\Core\Configuration;

use Yggdrasil\Core\Driver\Base\DriverCollection;
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
     * Gets given driver and returns it's component instance directly
     *
     * @param string $key Name of driver
     * @return mixed
     *
     * @throws DriverNotFoundException if given driver doesn't exist
     */
    public function loadDriver(string $key);

    /**
     * Returns configuration
     *
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * Checks if given data exist in configuration
     *
     * @param array  $keys    Set of keys of data to check
     * @param string $section Name of configuration file section, in which given keys should exist
     * @return bool
     */
    public function isConfigured(array $keys, string $section): bool;
}
