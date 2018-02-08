<?php

namespace Yggdrasil\Core\Driver\Base;

trait DriverAccessorTrait
{
    private $drivers;

    protected function getDriver($key)
    {
        return $this->drivers->get($key);
    }

    protected function getEntityManager()
    {
        return $this->drivers->get('entityManager');
    }

    protected function getTemplateEngine()
    {
        return $this->drivers->get('templateEngine');
    }

    protected function getRouter()
    {
        return $this->drivers->get('router');
    }

    protected function getValidator()
    {
        return $this->drivers->get('validator');
    }

    protected function getMailer()
    {
        return $this->drivers->get('mailer');
    }

    protected function getContainer()
    {
        return $this->drivers->get('container');
    }

    protected function getExceptionHandler()
    {
        return $this->drivers->get('exceptionHandler');
    }
}