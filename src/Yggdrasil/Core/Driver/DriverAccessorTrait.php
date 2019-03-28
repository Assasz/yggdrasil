<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Exception\DriverNotSupportedException;

/**
 * Trait DriverAccessorTrait
 *
 * Provides access to application drivers
 *
 * @see DriverCollection
 *
 * @package Yggdrasil\Core\Driver\Base
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
trait DriverAccessorTrait
{
    /**
     * Collection of application drivers
     *
     * @var DriverCollection
     */
    private $drivers;

    /**
     * Returns collection of all drivers
     *
     * @return DriverCollection
     */
    protected function getDrivers(): DriverCollection
    {
        return $this->drivers;
    }

    /**
     * Returns given driver instance
     *
     * @param string $key Name of driver
     * @return DriverInterface
     */
    protected function getDriver(string $key): DriverInterface
    {
        return $this->drivers->get($key);
    }

    /**
     * Returns router driver instance
     *
     * @return RouterDriver
     * @throws DriverNotSupportedException
     */
    protected function getRouter(): RouterDriver
    {
        if (!$this->drivers->get('router') instanceof RouterDriver) {
            throw new DriverNotSupportedException('Provided router driver is not supported.');
        }

        return $this->drivers->get('router');
    }

    /**
     * Returns template engine driver instance
     *
     * @return TemplateEngineDriver
     * @throws DriverNotSupportedException
     */
    protected function getTemplateEngine(): TemplateEngineDriver
    {
        if (!$this->drivers->get('templateEngine') instanceof TemplateEngineDriver) {
            throw new DriverNotSupportedException('Provided template engine driver is not supported.');
        }

        return $this->drivers->get('templateEngine');
    }

    /**
     * Returns repository provider instance
     *
     * @param string $driver
     * @return RepositoryProviderInterface
     */
    protected function getRepositoryProvider(string $driver): RepositoryProviderInterface
    {
        $repositoryProvider = $this->drivers->get($driver);

        if (!$repositoryProvider instanceof RepositoryProviderInterface) {
            throw new \InvalidArgumentException('Given driver is not a repository provider.');
        }

        return $repositoryProvider;
    }

    /**
     * Installs drivers in class by generating magic properties
     * Hint type of these properties by using '@property' tag
     *
     * @param array $drivers Drivers names, if NULL, all drivers will be installed
     */
    protected function installDrivers(array $drivers = null): void
    {
        $drivers = $drivers ?? $this->drivers;

        foreach ($drivers as $key => $driver) {
            if ($drivers instanceof DriverCollection) {
                $this->{$key} = $this->drivers->get($key);

                continue;
            }

            $this->{$driver} = $this->drivers->get($driver);
        }
    }
}
