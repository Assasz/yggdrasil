<?php

namespace Yggdrasil\Component\TwigComponent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class StandardExtension
 *
 * Provides standard extension for Twig
 *
 * @package Yggdrasil\Component\TwigComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class StandardExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * Returns set of globals
     *
     * @return array
     */
    public function getGlobals(): array
    {
        $session = new Session();

        return [
            '_session' => $session,
            '_user' => $session->get('user')
        ];
    }

    /**
     * Returns set of functions
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('csrf_token', [$this, 'generateCsrfToken']),
            new \Twig_Function('flashbag', [$this, 'getFlashBag']),
            new \Twig_Function('is_granted', [$this, 'isGranted']),
            new \Twig_Function('is_pjax', [$this, 'isPjax']),
            new \Twig_Function('partial', [$this, 'embedPartial'])
        ];
    }

    /**
     * Generates CSRF token
     *
     * @param bool $onlyToken Useful if multiple tokens are needed on one page
     * @return mixed
     *
     * @throws \Exception if any rand function can't be found in OS
     */
    public function generateCsrfToken(bool $onlyToken = false)
    {
        $token = bin2hex(random_bytes(32));

        $session = new Session();
        $session->set('csrf_token', $token);

        if ($onlyToken) {
            return $token;
        }

        echo '<input type="hidden" id="csrf_token" name="csrf_token" value="' . $token . '"/>';
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
        return (new Session())->getFlashBag()->get($type);
    }

    /**
     * Checks if user is authenticated
     *
     * @return bool
     */
    public function isGranted(): bool
    {
        return (new Session())->get('is_granted', false);
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
