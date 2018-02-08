<?php

namespace Yggdrasil\Core;

use Yggdrasil\Core\Driver\Base\DriverInstanceCollection;

abstract class AbstractConfiguration
{
    private $configuration;
    protected $drivers;

    public function __construct()
    {
        $configPath = dirname(__DIR__, 6).'/src/AppModule/Infrastructure/Config/config.ini';

        if(!file_exists($configPath)){
            //exception
        }

        $this->configuration = parse_ini_file($configPath, true);
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
            //exception
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