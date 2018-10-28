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
     * Returns configured instance of driver
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure driver
     * @return mixed
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): DriverInterface;
}
