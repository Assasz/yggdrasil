<?php

namespace Yggdrasil\Core\Driver\Abstraction;

/**
 * Class TemplateEngineDriver
 *
 * Abstract template engine driver
 *
 * @package Yggdrasil\Core\Driver\Abstraction
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
    public function render(string $view, array $params = []): string
    {
        return self::$engineInstance->render($view, $params);
    }

    /**
     * Displays given view
     *
     * @param string $view
     * @param array $params
     */
    public function display(string $view, array $params = []): void
    {
        self::$engineInstance->display($view, $params);
    }

    /**
     * Adds global to template engine
     *
     * @param string $name
     * @param mixed $value
     */
    public function addGlobal(string $name, $value): void
    {
        self::$engineInstance->addGlobal($name, $value);
    }
}