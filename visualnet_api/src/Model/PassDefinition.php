<?php

namespace Drupal\visualnet_api\Model;

/**
 * Class PassDefinition
 * @package Drupal\visualnet_api\Model
 */
class PassDefinition extends Entity
{
    protected $name;
    protected $price;

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
            'name', 'price',
        ], __CLASS__);

        $this->setProperties($entity, [
            'name', 'price',
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
    public function getPrice()
    {
        return $this->price;
    }

}
