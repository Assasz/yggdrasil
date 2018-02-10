<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Component\TwigComponent\TwigExtension;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class TemplateEngineDriver
 *
 * Template engine driver, necessary for templating to work
 * Twig is framework default template engine
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class TemplateEngineDriver implements DriverInterface
{
    /**
     * Instance of template engine
     *
     * @var \Twig_Environment
     */
    private static $engineInstance;

    /**
     * TemplateEngineDriver constructor.
     *
     * Should be private to prevent object creation. Same with __clone
     */
    private function __construct() {}

    private function __clone() {}

    /**
     * Returns instance of template engine
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure template engine
     * @return \Twig_Environment
     *
     * @throws MissingConfigurationException if view path is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): \Twig_Environment
    {
        if(self::$engineInstance === null) {
            $configuration = $appConfiguration->getConfiguration();

            if(!$appConfiguration->isConfigured(['view_path'], 'application')){
                throw new MissingConfigurationException('There are missing parameters in your configuration. view_path is required for template engine to work properly.');
            }

            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__, 7) . '/src/'.$configuration['application']['view_path']);
            $twig = new \Twig_Environment($loader);

            $twig->addExtension(new TwigExtension());

            self::$engineInstance = $twig;
        }

        return self::$engineInstance;
    }
}