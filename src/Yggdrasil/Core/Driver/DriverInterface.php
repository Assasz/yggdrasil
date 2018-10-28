<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;

/**
 * Interface DriverInterface
 *
 * Bridge between application and vendor
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface DriverInterface
{
    /**
     * Installs driver in application
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure driver
     * @return DriverInterface
     */
    public static function install(ConfigurationInterface $appConfiguration): DriverInterface;
}
