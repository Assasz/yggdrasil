<?php

namespace Yggdrasil\Core\Service;

interface ServiceInterface
{
    public function process(ServiceRequestInterface $request): ServiceResponseInterface;
}