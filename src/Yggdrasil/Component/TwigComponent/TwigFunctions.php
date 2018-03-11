<?php

namespace Yggdrasil\Component\TwigComponent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class TwigFunctions
 *
 * Provides set of functions for TwigExtension
 *
 * @package Yggdrasil\Component\TwigComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class TwigFunctions
{
    /**
     * Returns absolute path for requested action
     *
     * @param string $alias  Alias of action like Controller:action
     * @param array  $params Set of action parameters
     * @return string
     */
    public static function getPath(string $alias, array $params = []): string
    {
        $queryParams = explode(':', mb_strtolower($alias));

        foreach($params as $param){
            $queryParams[] = $param;
        }

        $query = implode('/', $queryParams);

        return BASE_URL.$query;
    }

    /**
     * Returns absolute path for requested asset like CSS file
     *
     * @param string $path
     * @return string
     */
    public static function getAsset(string $path): string
    {
        return BASE_URL.ltrim($path, '/');
    }

    /**
     * Generates CSRF token
     *
     * @throws \Exception if any rand function can't be found in OS
     */
    public static function getCsrfToken(): void
    {
        $token = bin2hex(random_bytes(32));

        $session = new Session();
        $session->set('csrf_token', $token);

        echo '<input type="hidden" id="csrf_token" name="csrf_token" value="'.$token.'"/>';
    }

    /**
     * Checks if website is requested with Pjax
     *
     * @param Request $request
     * @return bool
     */
    public static function isPjax(Request $request): bool
    {
        return ($request->headers->get('X-PJAX') !== null);
    }

    /**
     * Returns flash by type
     *
     * @param string $type
     * @return array
     */
    public static function getFlashBag(string $type): array
    {
        $session = new Session();
        return $session->getFlashBag()->get($type);
    }

    /**
     * Checks if user is authenticated
     *
     * @return bool
     */
    public static function isGranted(): bool
    {
        $session = new Session();
        return $session->get('is_granted', false);
    }

    /**
     * Embeds partial view
     *
     * @param string $view Rendered partial view
     */
    public static function partial(string $view): void
    {
        echo $view;
    }
}