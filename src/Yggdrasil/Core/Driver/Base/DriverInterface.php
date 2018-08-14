<?php

namespace Yggdrasil\Core\Driver\Base;

use Yggdrasil\Core\Configuration\ConfigurationInterface;

/**
 * Interface DriverInterface
 *
 * Implements singleton pattern in application driver
 *
 * @package Yggdrasil\Core\Driver\Base
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface DriverInterface
{
    /**
     * Returns instance of specific component (not driver itself)
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure component
     * @return mixed
     */
    public static function getInstance(ConfigurationInterface $appConfiguration);
}
