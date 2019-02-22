<?php

namespace Yggdrasil\Utils\Exception;

/**
 * Class BrokenContractException
 *
 * Thrown when contract between client and supplier has broken
 *
 * @package Yggdrasil\Utils\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class BrokenContractException extends \LogicException
{
    /**
     * BrokenContractException constructor.
     *
     * @param string      $contract Contract name
     * @param int         $code
     * @param \Throwable? $previous
     */
    public function __construct(string $contract, int $code = 0, \Throwable $previous = null)
    {
        $message = $contract . ' contract has broken.';

        parent::__construct($message, $code, $previous);
    }
}
