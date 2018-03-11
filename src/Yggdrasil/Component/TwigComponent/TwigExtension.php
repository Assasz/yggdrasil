<?php

namespace Yggdrasil\Component\TwigComponent;

/**
 * Class TwigExtension
 *
 * Provides extension for Twig
 *
 * @package Yggdrasil\Component\TwigComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * Returns set of functions
     *
     * @return array
     */
    public function getFunctions(): array
    {
        $namespace = 'Yggdrasil\Component\TwigComponent\TwigFunctions';

        return [
            new \Twig_Function('path', $namespace.'::getPath'),
            new \Twig_Function('asset', $namespace.'::getAsset'),
            new \Twig_Function('csrf_token', $namespace.'::getCsrfToken'),
            new \Twig_Function('flashbag', $namespace.'::getFlashBag'),
            new \Twig_Function('user', $namespace.'::getUser'),
            new \Twig_Function('is_granted', $namespace.'::isGranted'),
            new \Twig_Function('is_pjax', $namespace.'::isPjax'),
            new \Twig_Function('partial', $namespace.'::partial')
        ];
    }
}