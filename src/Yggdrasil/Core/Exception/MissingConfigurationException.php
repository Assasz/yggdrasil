<?php

namespace Yggdrasil\Core\Exception;

/**
 * Class MissingConfigurationException
 *
 * Thrown when there is missing configuration in application configuration file
 *
 * @package Yggdrasil\Core\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class MissingConfigurationException extends \LogicException
{
    /**
     * MissingConfigurationException constructor.
     *
     * @param array  $params  Parameters required in configuration
     * @param string $section Configuration section, where parameters are required
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(array $params, string $section, int $code = 0, \Throwable $previous = null)
    {
        $message = 'Some of required parameters are missing in configuration: ' . implode(', ', $params) . ' in section [' . $section . ']';

        parent::__construct($message, $code, $previous);
    }
}
