<?php

namespace Yggdrasil\Core;

interface DriverInterface
{
    public static function getInstance($configuration);
}