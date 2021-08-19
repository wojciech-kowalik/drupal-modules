<?php

namespace Drupal\visualnet_api\Model;

/**
 * Class Pass
 * @package Drupal\visualnet_api\Model
 */
class Pass extends Entity
{
    protected $barcode;
    protected $name;
    protected $price;
    protected $definition;

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
            'definition_id',
        ], __CLASS__);

        $this->checkIfPropertiesExists($content, ['passDefinitions'], __CLASS__);

        $this->setPropertyValue($entity, 'barcode');

    }

    /**
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->barcode;
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

    /**
     * @return mixed
     */
    public function getDefinition()
    {
        return new PassDefinition(
            $this->find($this->content->passDefinitions, $this->entity->definition_id),
            $this->content
        );
    }

}
