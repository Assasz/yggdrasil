<?php

namespace Yggdrasil\Core\Driver;

use Whoops\Run;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\ExceptionLogger;
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
     *
     * @throws MissingConfigurationException if handler or log_path is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): Run
    {
        if (self::$handlerInstance === null) {
            if (!$appConfiguration->isConfigured(['handler', 'log_path'], 'exception_handler')) {
                throw new MissingConfigurationException('There is missing parameter in your configuration: handler or log_path in exception_handler section.');
            }

            $configuration = $appConfiguration->getConfiguration();

            $run = new Run();

            if (DEBUG) {
                $handler = 'Whoops\Handler\\' . $configuration['exception_handler']['handler'] ?? 'PrettyPageHandler';
                $run->pushHandler(new $handler());
            } else {
                $run->pushHandler(static::getProdHandler($appConfiguration));
            }

            $logger = (new ExceptionLogger())
                ->setLogPath(dirname(__DIR__, 7) . '/src/' . $configuration['exception_handler']['log_path'] . '/exceptions.txt');

            $run->pushHandler(function ($exception) use ($logger) {
                $logger->log($exception);
            });

            $run->register();

            self::$handlerInstance = $run;
        }

        return self::$handlerInstance;
    }

    /**
     * Returns handler for production mode
     *
     * @param ConfigurationInterface $appConfiguration
     * @return \Closure
     */
    abstract protected static function getProdHandler(ConfigurationInterface $appConfiguration): \Closure;
}
