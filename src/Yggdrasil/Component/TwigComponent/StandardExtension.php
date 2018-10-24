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
     * Application name
     *
     * @var string
     */
    private $appName;

    /**
     * StandardExtension constructor.
     *
     * @param string $appName
     */
    public function __construct(string $appName)
    {
        $this->appName = $appName;
    }

    /**
     * Returns set of globals
     *
     * @return array
     */
    public function getGlobals(): array
    {
        $session = new Session();

        return [
            '_appname' => $this->appName,
            '_session' => $session,
            '_user'    => $session->get('user')
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
            new \Twig_Function('flashbag',   [$this, 'getFlashBag']),
            new \Twig_Function('is_granted', [$this, 'isGranted']),
            new \Twig_Function('is_pjax',    [$this, 'isPjax']),
            new \Twig_Function('is_yjax',    [$this, 'isYjax']),
            new \Twig_Function('partial',    [$this, 'embedPartial'])
        ];
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
     * Checks if view is requested with Pjax
     *
     * @param Request $request
     * @return bool
     */
    public function isPjax(Request $request): bool
    {
        return $request->headers->has('X-PJAX');
    }

    /**
     * Checks if view is requested with Yjax
     *
     * @param Request $request
     * @return bool
     */
    public function isYjax(Request $request): bool
    {
        return $request->headers->has('X-YJAX');
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
