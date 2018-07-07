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
     * Returns component instance of given driver
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
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->drivers->get('entityManager');
    }

    /**
     * Returns template engine instance
     *
     * @return \Twig_Environment
     */
    protected function getTemplateEngine(): \Twig_Environment
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
     * @return RecursiveValidator
     */
    protected function getValidator(): RecursiveValidator
    {
        return $this->drivers->get('validator');
    }

    /**
     * Returns mailer instance
     *
     * @return \Swift_Mailer
     */
    protected function getMailer(): \Swift_Mailer
    {
        return $this->drivers->get('mailer');
    }

    /**
     * Returns container instance
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->drivers->get('container');
    }

    /**
     * Returns exception handler instance
     *
     * @return Run
     */
    protected function getExceptionHandler(): Run
    {
        return $this->drivers->get('exceptionHandler');
    }
}
