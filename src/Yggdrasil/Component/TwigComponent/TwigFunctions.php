<?php

namespace Yggdrasil\Component\TwigComponent;

use AppModule\Infrastructure\Config\AppConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class TwigFunctions
{
    public static function getPath($alias, array $params = [])
    {
        $appConfig = new AppConfiguration();
        $router = $appConfig->loadDriver('router');

        return $router->getQuery($alias, $params);
    }

    public static function getAsset($path)
    {
        return BASE_URL.ltrim($path, '/');
    }

    public static function getCsrfToken()
    {
        $token = bin2hex(random_bytes(32));

        $session = new Session();
        $session->set('csrf_token', $token);

        echo '<input type="hidden" id="csrf_token" name="csrf_token" value="'.$token.'"/>';
    }

    public static function isPjax(Request $request)
    {
        return ($request->headers->get('X-PJAX') !== null);
    }

    public static function getFlashBag($type)
    {
        $session = new Session();
        return $session->getFlashBag()->get($type);
    }

    public static function isGranted()
    {
        $session = new Session();
        return $session->get('is_granted', false);
    }

    public static function getUser()
    {
        $session = new Session();
        return $session->get('user');
    }
}