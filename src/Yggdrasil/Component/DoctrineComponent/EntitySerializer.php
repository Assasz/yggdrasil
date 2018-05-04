<?php

namespace Yggdrasil\Component\DoctrineComponent;

use Doctrine\Common\Collections\Collection;

/**
 * Class EntitySerializer
 *
 * Serializes Doctrine entities into array
 *
 * @package Yggdrasil\Core\Helper
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class EntitySerializer
{
    /**
     * Does the job
     *
     * @param array $entities Array of entities to serialize
     * @param int   $depth    Association depth
     * @return array
     */
    public static function toArray(array $entities, int $depth = 1): array
    {
        if($depth < 0){
            return null;
        }

        $depth = $depth - 1;
        $data = [];
        $i = 0;

        foreach ($entities as $entity){
            $methods[$i] = get_class_methods($entity);

            foreach ($methods[$i] as $method){
                if(strpos($method, 'get') === false || strpos($method, 'get') !== 0){
                    continue;
                }

                $propertyName = lcfirst(substr($method, 3));

                $value = $entity->{$method}();

                if(is_object($value)){
                    if($value instanceof Collection){
                        $collection = $value->toArray();
                        $value = self::toArray($collection, $depth);
                    } elseif($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d H:i:s');
                    } else {
                        $value = self::toArray([$value], $depth);
                    }
                }

                $data[$i][$propertyName] = $value;
            }

            $i++;
        }

        return $data;
    }
}