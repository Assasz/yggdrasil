<?php

namespace Yggdrasil\Core\Service;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;

abstract class AbstractService
{
    use DriverAccessorTrait;

    public function __construct(ConfigurationInterface $appConfiguration)
    {
        $this->drivers = $appConfiguration->loadDrivers();
    }
}