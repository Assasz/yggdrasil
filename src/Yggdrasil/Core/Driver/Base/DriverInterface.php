<?php

namespace Yggdrasil\Core\Driver\Base;

use Yggdrasil\Core\Configuration\ConfigurationInterface;

interface DriverInterface
{
    public static function getInstance(ConfigurationInterface $appConfiguration);
}