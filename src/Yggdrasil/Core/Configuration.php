<?php

namespace Yggdrasil\Core;

class Configuration
{
    protected $configuration;
    protected $drivers;

    public function __construct()
    {
        $this->configuration = parse_ini_file(dirname(__DIR__, 6).'/src/AppModule/Infrastructure/Config/config.ini');
    }

    public function loadDrivers()
    {
        $driversInstances = [];

        foreach($this->drivers as $name => $driver){
            $driversInstances[$name] = $driver::getInstance($this->configuration);
        }

        return $driversInstances;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }
}