<?php

namespace Yggdrasil\Core\Driver\Base;

use Doctrine\ORM\EntityManager;
use League\Container\Container;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Whoops\Run;
use Yggdrasil\Core\Routing\Router;

/**
 * Trait DriverAccessorTrait
 *
 * Provides access to application drivers
 *
 * @see DriverInstanceCollection
 *
 * @package Yggdrasil\Core\Driver\Base
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
trait DriverAccessorTrait
{
    /**
     * Collection of drivers instances
     *
     * @var DriverInstanceCollection
     */
    private $drivers;

    /**
     * Returns given driver instance
     *
     * @param string $key Name of driver
     * @return mixed
     */
    protected function getDriver(string $key): mixed
    {
        return $this->drivers->get($key);
    }

    /**
     * Helper method that returns built-in driver instance, in this case entity manager
     *
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->drivers->get('entityManager');
    }

    /**
     * Helper method that returns built-in driver instance, in this case template engine
     *
     * @return \Twig_Environment
     */
    protected function getTemplateEngine(): \Twig_Environment
    {
        return $this->drivers->get('templateEngine');
    }

    /**
     * Helper method that returns built-in driver instance, in this case router
     *
     * @return Router
     */
    protected function getRouter(): Router
    {
        return $this->drivers->get('router');
    }

    /**
     * Helper method that returns built-in driver instance, in this case validator
     *
     * @return RecursiveValidator
     */
    protected function getValidator(): RecursiveValidator
    {
        return $this->drivers->get('validator');
    }

    /**
     * Helper method that returns built-in driver instance, in this case mailer
     *
     * @return \Swift_Mailer
     */
    protected function getMailer(): \Swift_Mailer
    {
        return $this->drivers->get('mailer');
    }

    /**
     * Helper method that returns built-in driver instance, in this case container
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->drivers->get('container');
    }

    /**
     * Helper method that returns built-in driver instance, in this case exception handler
     *
     * @return Run
     */
    protected function getExceptionHandler(): Run
    {
        return $this->drivers->get('exceptionHandler');
    }
}