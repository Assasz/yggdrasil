<?php

namespace Yggdrasil\Utils\Form;

/**
 * Class FormDataWrapper
 *
 * @package Yggdrasil\Utils\Form
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class FormDataWrapper
{
    /**
     * Wraps given DTO with form data collection
     *
     * @param object $dto
     * @param array $data
     * @return object
     */
    public static function wrap(object $dto, array $data): object
    {
        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);

            if (method_exists($dto, $setter)) {
                $dto->{$setter}($value);
            }
        }

        return $dto;
    }
}