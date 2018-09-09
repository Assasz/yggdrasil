<?php

namespace Yggdrasil\Core\Driver\Base;

use Yggdrasil\Core\Configuration\ConfigurationInterface;

/**
 * Interface DriverInterface
 *
 * Bridge between application and vendor
 *
 * @package Yggdrasil\Core\Driver\Base
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface DriverInterface
{
    /**
     * Returns configured instance of specific vendor component
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure component
     * @return mixed
     */
    public static function getInstance(ConfigurationInterface $appConfiguration);
}
