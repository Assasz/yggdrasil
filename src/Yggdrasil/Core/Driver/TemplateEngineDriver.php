<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Component\TwigComponent\FormExtension;
use Yggdrasil\Component\TwigComponent\RoutingExtension;
use Yggdrasil\Component\TwigComponent\StandardExtension;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class TemplateEngineDriver
 *
 * [Twig] Template Engine driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class TemplateEngineDriver implements DriverInterface
{
    /**
     * Instance of template engine
     *
     * @var \Twig_Environment
     */
    protected static $engineInstance;

    /**
     * Returns instance of template engine
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure template engine
     * @return \Twig_Environment
     *
     * @throws MissingConfigurationException if view_path, form_path or application_name is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): \Twig_Environment
    {
        if (self::$engineInstance === null) {
            $requiredConfig = ['view_path', 'form_path', 'application_name'];

            if (!$appConfiguration->isConfigured($requiredConfig, 'template_engine')) {
                throw new MissingConfigurationException($requiredConfig, 'template_engine');
            }

            $configuration = $appConfiguration->getConfiguration();

            $basePath = dirname(__DIR__, 7) . '/src/';
            $viewPath = $basePath . $configuration['template_engine']['view_path'];
            $formPath = $basePath . $configuration['template_engine']['form_path'];

            $loader = new \Twig_Loader_Filesystem($viewPath);
            $twig = new \Twig_Environment($loader, ['cache' => (!DEBUG) ? dirname(__DIR__, 7) . '/var/twig' : false]);

            $twig->addExtension(new StandardExtension($configuration['template_engine']['application_name']));
            $twig->addExtension(new RoutingExtension($appConfiguration->loadDriver('router')));
            $twig->addExtension(new FormExtension($formPath));

            foreach (self::getExtensionRegistry($appConfiguration) as $extension) {
                $twig->addExtension($extension);
            }

            self::$engineInstance = $twig;
        }

        return self::$engineInstance;
    }

    /**
     * Returns registered Twig extensions (excluding built-in)
     *
     * @param ConfigurationInterface $appConfiguration
     * @return array
     */
    protected static function getExtensionRegistry(ConfigurationInterface $appConfiguration): array
    {
        return [];
    }
}
