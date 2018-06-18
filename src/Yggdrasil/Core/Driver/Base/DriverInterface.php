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
     * Returns instance of component (not driver itself), that application want to use, e.g. instance of Entity Manager from EntityManagerDriver
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure component
     * @return mixed
     */
    public static function getInstance(ConfigurationInterface $appConfiguration);
}
