<?php

namespace Yggdrasil\Utils\Exception;

/**
 * Class NotServiceReturnedException
 *
 * Thrown when container returns object of another instance than AbstractService
 *
 * @package Yggdrasil\Utils\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class NotServiceReturnedException extends \InvalidArgumentException
{

}
