<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;

/**
 * Interface DriverInterface
 *
 * Anti-corruption layer component
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface DriverInterface
{
    /**
     * Installs driver in application
     *
     * @param ConfigurationInterface $configuration Configuration needed to configure driver
     * @return DriverInterface
     */
    public static function install(ConfigurationInterface $configuration): DriverInterface;
}
