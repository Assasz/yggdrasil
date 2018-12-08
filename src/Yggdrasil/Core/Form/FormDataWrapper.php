<?php

namespace Yggdrasil\Core\Form;

/**
 * Class FormDataWrapper
 *
 * @package Yggdrasil\Core\Form
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class FormDataWrapper
{
    /**
     * Wraps given object with form data collection
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