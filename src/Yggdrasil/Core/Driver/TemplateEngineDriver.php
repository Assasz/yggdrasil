<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Component\TwigComponent\RoutingExtension;
use Yggdrasil\Component\TwigComponent\StandardExtension;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * @throws MissingConfigurationException if view path or application_name is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): \Twig_Environment
    {
        if(self::$engineInstance === null) {
            $configuration = $appConfiguration->getConfiguration();

            if(!$appConfiguration->isConfigured(['view_path', 'application_name'], 'template_engine')){
                throw new MissingConfigurationException('There are missing parameters in your configuration: view_path or application_name in template_engine section.');
            }

            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__, 7) . '/src/'.$configuration['template_engine']['view_path']);
            $twig = new \Twig_Environment($loader);

            $twig->addExtension(new StandardExtension());
            $twig->addExtension(new RoutingExtension($appConfiguration->loadDriver('router')));

            $session = new Session();
            $twig->addGlobal('_session', $session);
            $twig->addGlobal('_user', $session->get('user'));
            $twig->addGlobal('_appname', $configuration['template_engine']['application_name']);

            self::$engineInstance = $twig;
        }

        return self::$engineInstance;
    }
}