<?php

namespace Yggdrasil\Component\TwigComponent;

use Yggdrasil\Core\Routing\Router;
use Symfony\Component\HttpFoundation\Session\Session;

class TwigFunctions
{
    public static function path($alias, array $params = [])
    {
        $router = new Router();
        return $router->getQuery($alias, $params);
    }

    public static function asset($path)
    {
        return BASE_URL.ltrim($path, '/');
    }

    public static function csrfToken()
    {
        $token = bin2hex(random_bytes(32));

        $session = new Session();
        $session->start();
        $session->set('csrf_token', $token);

        echo '<input type="hidden" id="csrf_token" name="csrf_token" value="'.$token.'"/>';
    }
}