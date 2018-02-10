<?php

namespace Yggdrasil\Core\Driver\Base;

use Yggdrasil\Core\Configuration\ConfigurationInterface;

/**
 * Interface DriverInterface
 *
 * Ensures that driver implements getInstance() method
 *
 * @package Yggdrasil\Core\Driver\Base
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface DriverInterface
{
    /**
     * Returns specific instance of object (not driver itself), that is used by application, e.g. entity manager
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure driver
     * @return mixed
     */
    public static function getInstance(ConfigurationInterface $appConfiguration);
}