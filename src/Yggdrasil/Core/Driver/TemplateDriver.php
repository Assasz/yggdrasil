<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Component\TwigComponent\TwigExtension;
use Yggdrasil\Core\Driver\DriverInterface;

class TemplateDriver implements DriverInterface
{
    private static $engineInstance;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance($configuration)
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