<?php

namespace Yggdrasil\Core\Controller;

/**
 * Interface ErrorControllerInterface
 *
 * @package Yggdrasil\Core\Controller
 */
interface ErrorControllerInterface
{
    /**
     * Default error action
     *
     * @return mixed Response object
     */
    public function defaultAction();
}
