<?php

namespace Yggdrasil\Core\Configuration;

use Yggdrasil\Core\Driver\DriverCollection;
use Yggdrasil\Core\Driver\DriverInterface;
use Yggdrasil\Core\Exception\ConfigurationNotFoundException;
use Yggdrasil\Core\Exception\DriverNotFoundException;

/**
 * Class AbstractConfiguration
 *
 * Manages configuration of particular application
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
     */
    public function __construct()
    {
        $this->configuration = $this->parseConfiguration();
        $this->drivers = $this->getDriverRegistry();
    }

    /**
     * Returns collection of registered application drivers
     *
     * @return DriverCollection
     */
    public function loadDrivers(): DriverCollection
    {
        $drivers = new DriverCollection($this);

        foreach ($this->drivers as $name => $driver) {
            $drivers->add($name, $driver);
        }

        return $drivers;
    }

    /**
     * Returns configured instance of given driver
     *
     * @param string $key Name of driver
     * @return DriverInterface
     *
     * @throws DriverNotFoundException if given driver doesn't exist
     */
    public function loadDriver(string $key): DriverInterface
    {
        if (!$this->hasDriver($key)) {
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that ' . $key . ' driver is properly configured.');
        }

        return $this->drivers[$key]::install($this);
    }

    /**
     * Checks if given driver exist in registry
     *
     * @param string $key Name of driver
     * @return bool
     */
    public function hasDriver(string $key): bool
    {
        return array_key_exists($key, $this->drivers);
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
     * Checks if given parameters exist in configuration
     *
     * @param array  $keys    Set of parameters keys to check
     * @param string $section Name of configuration file section, in which given parameters should exist
     * @return bool
     */
    public function isConfigured(array $keys, string $section): bool
    {
        if (!array_key_exists($section, $this->configuration)) {
            return false;
        }

        return !array_diff_key(array_flip($keys), $this->configuration[$section]);
    }

    /**
     * Parses config.ini file into configuration array
     *
     * @return array
     *
     * @throws ConfigurationNotFoundException if config.ini file doesn't exist in specified path
     */
    private function parseConfiguration(): array
    {
        $configFilePath = dirname(__DIR__, 7) . '/src/' . $this->getConfigPath() . '/config.ini';

        if (!file_exists($configFilePath)) {
            throw new ConfigurationNotFoundException('Configuration file in ' . $configFilePath . ' not found.');
        }

        return parse_ini_file($configFilePath, true);
    }

    /**
     * Returns application config path
     *
     * @return string
     */
    abstract protected function getConfigPath(): string;

    /**
     * Returns application drivers registry
     *
     * @example ['entityManager' => EntityManagerDriver::class]
     *
     * @return array
     */
    abstract protected function getDriversRegistry(): array;
}
