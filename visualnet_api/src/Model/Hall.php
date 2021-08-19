<?php

namespace Drupal\visualnet_api\Model;

/**
 * Class Hall
 * @package Drupal\visualnet_api\Model
 */
class Hall extends Entity
{
    protected $name;
    protected $type;
    protected $capacity;
    protected $location;

    public function __construct(\stdClass $entity, \stdClass $content)
    {
        $this->populate($entity, $content);
    }

    /**
     * @inheritdoc
     */
    protected function populate(\stdClass $entity, \stdClass $content)
    {
        parent::populate($entity, $content);

        $this->checkIfPropertiesExists($entity, [
            'name', 'type', 'capacity', 'location',
        ], __CLASS__);

        $this->checkIfPropertiesExists($content, ['locations']);

        $this->setProperties($entity, [
            'name', 'type', 'capacity',
        ]);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return new Location(
            $this->find($this->content->locations, $this->entity->location),
            $this->content
        );
    }

}
