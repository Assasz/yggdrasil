<?php

namespace Yggdrasil\Utils\Service;

/**
 * Class RequestWrapper
 *
 * @package Yggdrasil\Utils\Service
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class RequestWrapper
{
    /**
     * Wraps given service request (any DTO with setters) with data such as form data or request body
     *
     * @param object $request
     * @param array $data
     * @return object
     */
    public static function wrap(object $request, array $data): object
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