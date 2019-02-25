<?php

namespace Yggdrasil\Utils\Entity;

/**
 * Class EntitySerializer
 *
 * Serializes domain entities
 *
 * @package Yggdrasil\Utils\Entity
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class EntitySerializer
{
    /**
     * Serializes entities into array
     *
     * @param array $entities Array of entities to serialize
     * @param int   $depth    Entity association depth to be pursued by serialization
     * @return array
     */
    public static function toArray(array $entities, int $depth = 1): array
    {
        if ($depth < 0) {
            return null;
        }

        $depth = $depth - 1;
        $data = [];
        $i = 0;

        foreach ($entities as $entity) {
            if (!is_object($entity)) {
                continue;
            }

            $methods[$i] = get_class_methods($entity);

            foreach ($methods[$i] as $method) {
                if (
                  (strpos($method, 'get') === false || strpos($method, 'get') !== 0) &&
                  (strpos($method, 'is') === false || strpos($method, 'is') !== 0)
                ) {
                    continue;
                }

                $propertyName = lcfirst(substr($method, (strpos($method, 'is') === 0) ? 2 : 3));

                $value = $entity->{$method}();

                if (is_object($value)) {
                    ($value instanceof \DateTime) ?
                        $value = $value->format('Y-m-d H:i:s') :
                        $value = self::toArray([$value], $depth);
                }

                $data[$i][$propertyName] = $value;
            }

            $i++;
        }

        return $data;
    }
}
