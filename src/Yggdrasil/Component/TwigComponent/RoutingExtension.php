<?php

namespace Yggdrasil\Component\TwigComponent;

use Yggdrasil\Core\Routing\Router;

/**
 * Class RoutingExtension
 *
 * Provides routing extension for Twig
 *
 * @package Yggdrasil\Component\TwigComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class RoutingExtension extends \Twig_Extension
{
    /**
     * Router instance
     *
     * @var Router
     */
    private $router;

    /**
     * RoutingExtension constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Returns set of functions
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('path', [$this, 'getPath'])
        ];
    }

    /**
     * Returns absolute path for requested action
     *
     * @param string $alias  Alias of action like Controller:action
     * @param array  $params Set of action parameters
     * @return string
     */
    public function getPath(string $alias, array $params = []): string
    {
        return $this->router->getQuery($alias, $params);
    }
}