<?php

namespace Yggdrasil\Core;

use Yggdrasil\Core\Routing\Router;

class TwigExtension
{
    public static function path($alias, array $params = [])
    {
        $router = new Router();
        return $router->getQuery($alias, $params);
    }
}