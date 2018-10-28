<?php

namespace Yggdrasil\Core\Driver;

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
     * Returns entity manager driver instance
     *
     * @return DriverInterface
     */
    protected function getEntityManager(): DriverInterface
    {
        return $this->drivers->get('entityManager');
    }

    /**
     * Returns template engine driver instance
     *
     * @return DriverInterface
     */
    protected function getTemplateEngine(): DriverInterface
    {
        return $this->drivers->get('templateEngine');
    }

    /**
     * Returns router driver instance
     *
     * @return DriverInterface
     */
    protected function getRouter(): DriverInterface
    {
        return $this->drivers->get('router');
    }

    /**
     * Returns validator driver instance
     *
     * @return DriverInterface
     */
    protected function getValidator(): DriverInterface
    {
        return $this->drivers->get('validator');
    }

    /**
     * Returns mailer driver instance
     *
     * @return DriverInterface
     */
    protected function getMailer(): DriverInterface
    {
        return $this->drivers->get('mailer');
    }

    /**
     * Returns container driver instance
     *
     * @return DriverInterface
     */
    protected function getContainer(): DriverInterface
    {
        return $this->drivers->get('container');
    }

    /**
     * Returns exception handler driver instance
     *
     * @return DriverInterface
     */
    protected function getExceptionHandler(): DriverInterface
    {
        return $this->drivers->get('exceptionHandler');
    }

    /**
     * Returns cache driver instance
     *
     * @return DriverInterface
     */
    protected function getCache(): DriverInterface
    {
        return $this->drivers->get('cache');
    }
}
