<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Whoops\Run;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class ExceptionHandlerDriver
 *
 * Exception handler driver, necessary for exception handling to work
 * Whoops is framework default exception handler
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class ExceptionHandlerDriver implements DriverInterface
{
    /**
     * Instance of exception handler
     *
     * @var Run
     */
    private static $handlerInstance;

    /**
     * ExceptionHandlerDriver constructor.
     *
     * Should be private to prevent object creation. Same with __clone
     */
    private function __construct() {}

    private function __clone() {}

    /**
     * Returns instance of exception handler
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure exception handler
     * @return Run
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): Run
    {
        if (self::$handlerInstance === null) {
            $configuration = $appConfiguration->getConfiguration();

            if (!$appConfiguration->isConfigured(['handler'], 'exception_handler')) {
                throw new MissingConfigurationException('There is missing parameter in your configuration: handler in exception_handler section.');
            }

            $run = new Run();

            if (DEBUG) {
                $handler = 'Whoops\Handler\\' . $configuration['exception_handler']['handler'] ?? 'PrettyPageHandler';
                $run->pushHandler(new $handler());
            } else {
                $run->pushHandler(function () {
                    echo 'Internal server error.';
                });
            }

            $run->register();

            self::$handlerInstance = $run;
        }

        return self::$handlerInstance;
    }
}
