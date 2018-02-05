<?php

namespace Yggdrasil\Component\TwigComponent;

class TwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        $namespace = 'Yggdrasil\Component\TwigComponent\TwigFunctions';

        return [
            new \Twig_Function('path', $namespace.'::getPath'),
            new \Twig_Function('asset', $namespace.'::getAsset'),
            new \Twig_Function('csrf_token', $namespace.'::getCsrfToken'),
            new \Twig_Function('flashbag', $namespace.'::getFlashBag'),
            new \Twig_Function('user', $namespace.'::getUser'),
            new \Twig_Function('is_granted', $namespace.'::isGranted'),
            new \Twig_Function('is_pjax', $namespace.'::isPjax')
        ];
    }
}