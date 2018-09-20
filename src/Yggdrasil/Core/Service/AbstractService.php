<?php

namespace Yggdrasil\Core\Service;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;

/**
 * Class AbstractService
 *
 * Base class for application services
 *
 * @package Yggdrasil\Core\Service
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class AbstractService
{
    /**
     * Trait that provides access to drivers
     */
    use DriverAccessorTrait;

    /**
     * AbstractService constructor.
     *
     * Loads drivers from configuration
     *
     * @param ConfigurationInterface $appConfiguration Configuration passed by ContainerDriver
     */
    public function __construct(ConfigurationInterface $appConfiguration)
    {
        $this->drivers = $appConfiguration->loadDrivers();
    }
}
