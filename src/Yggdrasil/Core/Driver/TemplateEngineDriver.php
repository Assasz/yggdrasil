<?php

namespace Yggdrasil\Core\Driver;

use AppModule\Infrastructure\Config\AppConfiguration;
use Yggdrasil\Component\TwigComponent\TwigExtension;
use Yggdrasil\Core\Driver\Base\DriverInterface;

class TemplateEngineDriver implements DriverInterface
{
    private static $engineInstance;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance(AppConfiguration $appConfiguration)
    {
        if(self::$engineInstance === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__, 7) . '/src/AppModule/Infrastructure/View');
            $twig = new \Twig_Environment($loader);

            $twig->addExtension(new TwigExtension());

            self::$engineInstance = $twig;
        }

        return self::$engineInstance;
    }
}