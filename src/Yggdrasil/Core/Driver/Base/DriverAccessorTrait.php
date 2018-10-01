<?php

namespace Yggdrasil\Core\Driver\Base;

use Yggdrasil\Core\Exception\NotServiceReturnedException;
use Yggdrasil\Core\Exception\ServiceNotFoundException;
use Yggdrasil\Core\Routing\Router;
use Yggdrasil\Core\Service\ServiceInterface;

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
     * Returns component instance configured by given driver
     *
     * @param string $key Name of driver
     * @return mixed
     */
    protected function getDriver(string $key)
    {
        return $this->drivers->get($key);
    }

    /**
     * Returns entity manager instance
     *
     * @return mixed
     */
    protected function getEntityManager()
    {
        return $this->drivers->get('entityManager');
    }

    /**
     * Returns template engine instance
     *
     * @return mixed
     */
    protected function getTemplateEngine()
    {
        return $this->drivers->get('templateEngine');
    }

    /**
     * Returns router instance
     *
     * @return Router
     */
    protected function getRouter(): Router
    {
        return $this->drivers->get('router');
    }

    /**
     * Returns validator instance
     *
     * @return mixed
     */
    protected function getValidator()
    {
        return $this->drivers->get('validator');
    }

    /**
     * Returns mailer instance
     *
     * @return mixed
     */
    protected function getMailer()
    {
        return $this->drivers->get('mailer');
    }

    /**
     * Returns container instance
     *
     * @return mixed
     */
    protected function getContainer()
    {
        return $this->drivers->get('container');
    }

    /**
     * Returns exception handler instance
     *
     * @return mixed
     */
    protected function getExceptionHandler()
    {
        return $this->drivers->get('exceptionHandler');
    }

    /**
     * Returns cache instance
     *
     * @return mixed
     */
    protected function getCache()
    {
        return $this->drivers->get('cache');
    }

    /**
     * Helper method that returns given service instance from container directly
     *
     * @param string $alias Alias of service like module.service_name
     * @return ServiceInterface
     *
     * @throws ServiceNotFoundException if given service doesn't exist
     * @throws NotServiceReturnedException if object returned by container is not a service
     * @throws \Exception
     */
    protected function getService(string $alias): ServiceInterface
    {
        if (!$this->getContainer()->has($alias)) {
            throw new ServiceNotFoundException('Service with alias ' . $alias . ' doesn\'t exist.');
        }

        if (!$this->getContainer()->get($alias) instanceof ServiceInterface) {
            throw new NotServiceReturnedException('Not a service returned by container for alias ' . $alias . '.');
        }

        return $this->getContainer()->get($alias);
    }
}
