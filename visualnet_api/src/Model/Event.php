<?php

namespace Drupal\visualnet_api\Model;

/**
 * Class Event
 * @package Drupal\visualnet_api\Model
 */
class Event extends Entity
{
    protected $id;
    protected $title;
    protected $duration;
    protected $originalTitle;
    protected $locale;
    protected $year;

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
            'title', 'oryginalTitle', 'duration',
        ], __CLASS__);

        $this->originalTitle = $entity->oryginalTitle;

        $this->setProperties($entity, [
            'id', 'title', 'duration', 'locale', 'year',
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
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return mixed
     */
    public function getOriginalTitle()
    {
        return $this->originalTitle;
    }

}
