<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Whoops\Run;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class ExceptionHandlerDriver
 *
 * [Whoops] Exception Handler driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class ExceptionHandlerDriver implements DriverInterface
{
    /**
     * Instance of exception handler
     *
     * @var Run
     */
    protected static $handlerInstance;

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
