<?php

namespace Yggdrasil\Core\Service\Utils;

use Yggdrasil\Core\Service\ServiceRequestInterface;

/**
 * Class ServiceRequestWrapper
 *
 * @package Yggdrasil\Core\Service\Utils
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class ServiceRequestWrapper
{
    /**
     * Wraps service request in data collection
     *
     * @param ServiceRequestInterface $request
     * @param array $data
     * @return ServiceRequestInterface
     */
    public static function wrap(ServiceRequestInterface $request, array $data): ServiceRequestInterface
    {
        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);

            if (method_exists($request, $setter)) {
                $request->{$setter}($value);
            }
        }

        return $request;
    }
}