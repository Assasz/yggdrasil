<?php

namespace Yggdrasil\Core\Exception;

/**
 * Class NotServiceReturnedException
 *
 * Thrown when container returns object of another instance than ServiceInterface
 *
 * @package Yggdrasil\Core\Exception
 */
class NotServiceReturnedException extends \InvalidArgumentException
{

}