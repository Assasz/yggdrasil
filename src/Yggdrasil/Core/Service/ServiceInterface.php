<?php

namespace Yggdrasil\Core\Service;

/**
 * Interface ServiceInterface
 *
 * Interactor between input port and output port of service
 * Ensures that service implements process() method which receives request and returns response
 *
 * @package Yggdrasil\Core\Service
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface ServiceInterface
{
    /**
     * Processes service task and returns specified response
     *
     * @param ServiceRequestInterface $request Input port request
     * @return ServiceResponseInterface        Output port response
     */
    public function process(ServiceRequestInterface $request): ServiceResponseInterface;
}