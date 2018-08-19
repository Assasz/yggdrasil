<?php

namespace Yggdrasil\Core\Exception;

/**
 * Class NotDriverProvidedException
 *
 * Thrown when object provided to DriverCollection is of another instance than DriverInterface
 *
 * @package Yggdrasil\Core\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class NotDriverProvidedException extends \InvalidArgumentException
{

}