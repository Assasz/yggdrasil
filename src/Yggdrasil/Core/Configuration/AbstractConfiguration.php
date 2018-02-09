<?php

namespace Yggdrasil\Core\Configuration;

use Yggdrasil\Core\Driver\Base\DriverInstanceCollection;
use Yggdrasil\Core\Exception\ConfigurationNotFoundException;
use Yggdrasil\Core\Exception\DriverNotFoundException;

abstract class AbstractConfiguration
{
    private $configuration;
    protected $drivers;

    public function __construct($configPath)
    {
        $configFilePath = dirname(__DIR__, 7).'/src/'.$configPath.'/config.ini';

        if(!file_exists($configFilePath)){
            throw new ConfigurationNotFoundException('Configuration file in '.$configFilePath.' not found.');
        }

        $this->configuration = parse_ini_file($configFilePath, true);
    }

    public function loadDrivers()
    {
        $driversInstances = new DriverInstanceCollection();

        foreach($this->drivers as $name => $driver){
            $driversInstances->add($name, $driver::getInstance($this));
        }

        return $driversInstances;
    }

    public function loadDriver($key)
    {
        if(!array_key_exists($key, $this->drivers)){
            throw new DriverNotFoundException('Driver you are looking for doesn\'t exist. Make sure that '.$key.' driver is configured.');
        }

        return $this->drivers[$key]::getInstance($this);
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function isConfigured(array $keys, $section)
    {
        if(!array_key_exists($section, $this->configuration)){
            return false;
        }

        return !array_diff_key(array_flip($keys), $this->configuration[$section]);
    }
}