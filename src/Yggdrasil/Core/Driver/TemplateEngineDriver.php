<?php

namespace Yggdrasil\Core\Driver;

/**
 * Class TemplateEngineDriver
 *
 * Abstract template engine driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class TemplateEngineDriver
{
    /**
     * Instance of template engine
     *
     * @var mixed
     */
    protected static $engineInstance;

    /**
     * Renders given view
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    abstract public function render(string $view, array $params = []): string;

    /**
     * Adds global to template engine
     *
     * @param string $name
     * @param mixed $value
     */
    abstract public function addGlobal(string $name, $value): void;
}
