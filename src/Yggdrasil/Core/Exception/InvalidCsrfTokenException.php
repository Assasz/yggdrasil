<?php

namespace Yggdrasil\Core\Exception;

/**
 * Class InvalidCsrfTokenException
 *
 * Thrown when received CSRF token doesn't match token stored in session
 *
 * @package Yggdrasil\Core\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class InvalidCsrfTokenException extends \RuntimeException
{

}
