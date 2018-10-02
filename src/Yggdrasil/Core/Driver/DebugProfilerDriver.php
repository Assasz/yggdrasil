<?php

namespace Yggdrasil\Core\Driver;

use Symfony\Component\HttpFoundation\Request;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Debug\DebugProfiler;
use Yggdrasil\Core\Debug\RequestDataCollector;
use Yggdrasil\Core\Driver\Base\DriverInterface;

/**
 * Class DebugProfilerDriver
 *
 * [Yggdrasil] Debug profiler driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class DebugProfilerDriver implements DriverInterface
{
    /**
     * Instance of profiler
     *
     * @var DebugProfiler
     */
    protected static $profilerInstance;

    /**
     * Returns instance of profiler
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure profiler
     * @return DebugProfiler
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): DebugProfiler
    {
        if (self::$profilerInstance === null) {
            $profiler = (new DebugProfiler())
                ->addDataCollector(new RequestDataCollector(), Request::createFromGlobals());

            self::$profilerInstance = $profiler;
        }

        return self::$profilerInstance;
    }
}
