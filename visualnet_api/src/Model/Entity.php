<?php

namespace Drupal\visualnet_api\Model;

use Assert\Assertion;

/**
 * Class Entity
 * @package Drupal\visualnet_api\Model
 */
abstract class Entity
{
    protected $entity;
    protected $content;

    protected $primaryKey = 'id';

    /**
     * @param $entities
     * @param $id
     *
     * @return mixed
     */
    protected function find(array $entities, $id)
    {
        $pk = $this->primaryKey;

        $searched = array_filter($entities,
            function ($entity) use ($id, $pk) {
                return ($id == $entity->{$pk});
            }
        );

        return array_values($searched)[0];
    }

    /**
     * @param \stdClass $entity
     * @param array     $properties
     * @param string    $className
     */
    protected function checkIfPropertiesExists(
        \stdClass $entity,
        array $properties,
        $className = __CLASS__
    ) {
        foreach ($properties as $property) {
            Assertion::propertyExists(
                $entity,
                $property,
                'No property ' . $property . ' found in ' . $className
            );
        }
    }

    /**
     * @param $object
     * @param $name
     *
     * @return mixed
     */
    protected function setPropertyValue($object, $name)
    {
        $this->{$name} = (isset($object->{$name})) ? $object->{$name} : null;
    }

    /**
     * @param       $object
     * @param array $names
     */
    protected function setProperties($object, array $names)
    {
        foreach ($names as $name) {
            $this->setPropertyValue($object, $name);
        }
    }

    /**
     * @param \stdClass $entity
     * @param \stdClass $content
     *
     * @return mixed
     */
    protected function populate(\stdClass $entity, \stdClass $content)
    {
        $this->entity  = $entity;
        $this->content = $content;
    }

    public function __destruct()
    {
        unset($this->entity);
        unset($this->content);
    }

}
