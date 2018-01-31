<?php

namespace Yggdrasil\Core\Routing;

class Route
{
    private $controller;
    private $action;
    private $actionParams;

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getActionParams()
    {
        return $this->actionParams;
    }

    public function setActionParams(array $actionParams)
    {
        $this->actionParams = $actionParams;
    }
}