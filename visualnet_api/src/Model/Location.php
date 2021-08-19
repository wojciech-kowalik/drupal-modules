<?php

namespace Drupal\visualnet_api\Model;

/**
 * Class Location
 * @package Drupal\visualnet_api\Model
 */
class Location extends Entity
{
    protected $name;
    protected $city;
    protected $zip;
    protected $address;
    protected $phone;
    protected $wepPage;
    protected $country;

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
            'name', 'city', 'zip', 'phone', 'country',
        ], __CLASS__);

        $this->checkIfPropertiesExists($content, ['locations']);

        $this->address = $entity->addresse;

        $this->setProperties($entity, [
            'name', 'city', 'phone', 'webPage', 'country',
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
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getWepPage()
    {
        return $this->wepPage;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

}
