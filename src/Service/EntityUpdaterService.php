<?php
namespace App\Service;

class EntityUpdaterService
{
    /**
     * Update an entity from keys passed to the data array.
     *
     * @param object $entity An entity
     * @param array $data    Associated entity data like "property" => "value"
     * @return object        The updated entities
     */
    public function update(object $entity, array $data): object
    {
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($entity,$method)) {
                $entity->$method($value);
            }
        }
        return $entity;
    }
}