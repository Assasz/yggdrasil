<?php

namespace Yggdrasil\Component\TwigComponent;

class TwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        $namespace = 'Yggdrasil\Component\TwigComponent\TwigFunctions';

        return [
            new \Twig_Function('path', $namespace.'::path'),
            new \Twig_Function('asset', $namespace.'::asset'),
            new \Twig_Function('csrf_token', $namespace.'::csrfToken')
        ];
    }
}