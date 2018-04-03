<?php

namespace Yggdrasil\Component\TwigComponent;

/**
 * Class StandardExtension
 *
 * Provides standard extension for Twig
 *
 * @package Yggdrasil\Component\TwigComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class StandardExtension extends \Twig_Extension
{
    /**
     * Returns set of functions
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('asset', [$this, 'getAsset']),
            new \Twig_Function('csrf_token', [$this, 'generateCsrfToken']),
            new \Twig_Function('flashbag', [$this, 'getFlashBag']),
            new \Twig_Function('is_granted', [$this, 'isGranted']),
            new \Twig_Function('is_pjax', [$this, 'isPjax']),
            new \Twig_Function('partial', [$this, 'embedPartial'])
        ];
    }

    /**
     * Returns absolute path for requested asset like CSS file
     *
     * @param string $path
     * @return string
     */
    public function getAsset(string $path): string
    {
        return BASE_URL.ltrim($path, '/');
    }

    /**
     * Generates CSRF token
     *
     * @throws \Exception if any rand function can't be found in OS
     */
    public function generateCsrfToken(): void
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
    public function isPjax(Request $request): bool
    {
        return ($request->headers->get('X-PJAX') !== null);
    }

    /**
     * Returns flash by type
     *
     * @param string $type
     * @return array
     */
    public function getFlashBag(string $type): array
    {
        $session = new Session();
        return $session->getFlashBag()->get($type);
    }

    /**
     * Checks if user is authenticated
     *
     * @return bool
     */
    public function isGranted(): bool
    {
        $session = new Session();
        return $session->get('is_granted', false);
    }

    /**
     * Embeds partial view
     *
     * @param string $view Rendered partial view
     */
    public function embedPartial(string $view): void
    {
        echo $view;
    }
}