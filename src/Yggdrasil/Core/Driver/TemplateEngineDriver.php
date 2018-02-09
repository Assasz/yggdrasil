<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Component\TwigComponent\TwigExtension;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;

class TemplateEngineDriver implements DriverInterface
{
    private static $engineInstance;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance(ConfigurationInterface $appConfiguration)
    {
        if(self::$engineInstance === null) {
            $configuration = $appConfiguration->getConfiguration();

            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__, 7) . '/src/'.$configuration['application']['view_path']);
            $twig = new \Twig_Environment($loader);

            $twig->addExtension(new TwigExtension());

            self::$engineInstance = $twig;
        }

        return self::$engineInstance;
    }
}