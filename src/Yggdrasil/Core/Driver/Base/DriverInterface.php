<?php

namespace Yggdrasil\Core\Driver\Base;

use AppModule\Infrastructure\Config\AppConfiguration;

interface DriverInterface
{
    public static function getInstance(AppConfiguration $appConfiguration);
}