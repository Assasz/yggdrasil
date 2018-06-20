<?php

namespace Yggdrasil\Core\Configuration;

use Yggdrasil\Core\Driver\Base\DriverCollection;
use Yggdrasil\Core\Exception\ConfigurationNotFoundException;
use Yggdrasil\Core\Exception\DriverNotFoundException;

/**
 * Class AbstractConfiguration
 *
 * Manages configuration and drivers, that application uses
 *
 * @package Yggdrasil\Core\Configuration
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class AbstractConfiguration
{
    /**
     * Configuration of application
     *
     * @var array
     */
    private $configuration;

    /**
     * Set of registered drivers
     *
     * @var array
     */
    protected $drivers;

    /**
     * AbstractConfiguration constructor.
     *
     * Gets configuration from configuration file specified in $configPath
     *
     * @param string $configPath Path of configuration file
     */
    public function __construct(string $configPath)
    {
        $configFilePath = dirname(__DIR__, 7) . '/src/' . $configPath . '/config.ini';

        if (!file_exists($configFilePath)) {
            throw new ConfigurationNotFoundException('Configuration file in ' . $configFilePath . ' not found.');
        }

        $this->configuration = parse_ini_file($configFilePath, true);
    }

    /**
     * Returns collection of application drivers
     *
     * @return DriverCollection
     */
    public function loadDrivers(): DriverCollection
    {
        $driversInstances = new DriverCollection($this);

        foreach ($this->drivers as $name => $driver) {
            $driversInstances->add($name, $driver);
        }

        return $driversInstances;
    }

    /**
     * Gets given driver and returns it's component instance directly
     *
     * @param string $key Name of driver
     * @return mixed
     *
     * @throws DriverNotFoundException if given driver doesn't exist
     */
    public function loadDriver(string $key)
    {
        if (!array_key_exists($key, $this->drivers)) {
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that ' . $key . ' driver is properly configured.');
        }

        return $this->drivers[$key]::getInstance($this);
    }

    /**
     * Returns configuration
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * Checks if given data exist in configuration
     *
     * @param array  $keys    Set of keys of data to check
     * @param string $section Name of configuration file section, in which given keys should exist
     * @return bool
     */
    public function isConfigured(array $keys, string $section): bool
    {
        if (!array_key_exists($section, $this->configuration)) {
            return false;
        }

        return !array_diff_key(array_flip($keys), $this->configuration[$section]);
    }
}
