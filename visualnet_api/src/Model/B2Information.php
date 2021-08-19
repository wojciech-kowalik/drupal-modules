<?php

namespace Drupal\visualnet_api\Model;

/**
 * Class B2Information
 * @package Drupal\visualnet_api\Model
 */
class B2Information extends Entity
{
    protected $id;
    protected $date;
    protected $availableTickets;
    protected $info;

    public function __construct(\stdClass $entity, \stdClass $content)
    {
        $this->populate($entity, $content);
    }

    /**
     * @inheritdoc
     */
    public function populate(\stdClass $entity, \stdClass $content)
    {
        parent::populate($entity, $content);

        $this->checkIfPropertiesExists($entity, [
            'availableTickets', 'info',
        ], __CLASS__);

        $this->setProperties($entity, [
            'id', 'date', 'availableTickets', 'info',
        ]);

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getAvailableTickets()
    {
        return $this->availableTickets;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

}
