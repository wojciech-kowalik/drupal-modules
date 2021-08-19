<?php

namespace Drupal\visualnet_api\Model;

/**
 * Class Order
 * @package Drupal\visualnet_api\Model
 */
class Order extends Entity
{
    protected $barcode;
    protected $createdAt;
    protected $unix;
    protected $statusName;
    protected $amount;

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
            'barcode', 'createdAt', 'statusName', 'amount',
        ], __CLASS__);

        $this->setProperties($entity, [
            'barcode', 'createdAt', 'statusName', 'amount',
        ]);

        $this->unix = strtotime($entity->createdAt);

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
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getUnixDate()
    {
        return $this->unix;
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        return $this->statusName;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

}
