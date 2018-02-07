<?php

namespace Yggdrasil\Core\Service;

use AppModule\Infrastructure\Config\AppConfiguration;

abstract class AbstractService
{
    private $drivers;

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

    protected function getValidator()
    {
        return $this->drivers['validator'];
    }

    protected function getMailer()
    {
        return $this->drivers['mailer'];
    }

    protected function getContainer()
    {
        return $this->drivers['container'];
    }
}