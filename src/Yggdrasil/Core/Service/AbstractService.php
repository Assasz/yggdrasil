<?php

namespace Yggdrasil\Core\Service;

use AppModule\Infrastructure\Config\AppConfiguration;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;

abstract class AbstractService
{
    use DriverAccessorTrait;

    public function __construct(AppConfiguration $appConfiguration)
    {
        $this->drivers = $appConfiguration->loadDrivers();
    }
}