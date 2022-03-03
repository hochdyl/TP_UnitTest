<?php
namespace App\Service;

class EntityUpdater
{
    /**
     * Update an entity from keys passed to the data array.
     *
     * @param array $entities A collection of entities
     * @param array $data Associated data array like "property" => "value"
     * @return array The updated entities
     */
    public function update(array $entities, array $data): array
    {
        foreach($data as $key => $value) {
            $method = 'set'.ucfirst($key);
            foreach($entities as $entity)
            if (method_exists($entity,$method)) {
                $entity->$method($value);
            }
        }
        return $entities;
    }
}