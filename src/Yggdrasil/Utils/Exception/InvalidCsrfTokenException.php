<?php

namespace Yggdrasil\Utils\Exception;

/**
 * Class InvalidCsrfTokenException
 *
 * Thrown when received CSRF token doesn't match token stored in session
 *
 * @package Yggdrasil\Utils\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class InvalidCsrfTokenException extends \RuntimeException
{
    /**
     * InvalidCsrfTokenException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable? $previous
     */
    public function __construct(string $message = "Invalid CSRF token.", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
