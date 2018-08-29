<?php

namespace Yggdrasil\Core\Exception;

/**
 * Class ActionForbiddenException
 *
 * Thrown when requested action is partial, passive or belongs to ErrorController
 *
 * @package Yggdrasil\Core\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class ActionForbiddenException extends \LogicException
{

}
