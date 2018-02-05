<?php

namespace Yggdrasil\Core\Service;

use AppModule\Infrastructure\Config\AppConfiguration;

abstract class AbstractService
{
    protected $drivers;

    public function __construct(AppConfiguration $configuration)
    {
        $this->drivers = $configuration->loadDrivers();
    }

    protected function getDriver($name)
    {
        return $this->drivers[$name];
    }

    protected function getEntityManager()
    {
        return $this->drivers['entityManager'];
    }
}