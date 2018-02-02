<?php

namespace Yggdrasil\Core\Driver;

interface DriverInterface
{
    public static function getInstance($configuration);
}