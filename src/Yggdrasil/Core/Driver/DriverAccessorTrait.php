<?php

namespace Yggdrasil\Core\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Yggdrasil\Core\Annotation\Drivers;
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
     * Installs drivers enabled by class annotation by generating magic properties
     * Hint type of these properties by using '@property' tag
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    protected function installDriversIfEnabled(): void
    {
        $reflection = new \ReflectionClass($this);
        $reader = new AnnotationReader();

        $annotation = $reader->getClassAnnotation($reflection, Drivers::class);

        if (empty($annotation)) {
            return;
        }

        foreach ($annotation->install as $driver) {
            $this->{$driver} = $this->drivers->get($driver);
        }
    }
}
