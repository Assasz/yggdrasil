<?php

namespace Yggdrasil\Component\NidhoggComponent\Exception;

/**
 * Class UnsupportedSessionProviderException
 *
 * Thrown when configured session provider is unsupported by Nidhogg
 *
 * @package Yggdrasil\Component\NidhoggComponent\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class UnsupportedSessionProviderException extends \LogicException
{
    /**
     * UnsupportedSessionProviderException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable? $previous
     */
    public function __construct(string $message = "Seems that your cache driver is configured to use another cache than Redis. Currently only Redis is supported as session provider.", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}