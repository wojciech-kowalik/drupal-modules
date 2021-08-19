<?php

namespace Drupal\visualnet_api\Model;

use Drupal\visualnet_cart\Service\ButtonService;
use Moment\Moment;

/**
 * Class Repertoire
 * @package Drupal\visualnet_api\Model
 */
class Repertoire extends Entity
{
    protected $date;
    protected $unix;
    protected $publish;
    protected $b2Id;
    protected $event;
    protected $hall;
    protected $b2Info;
    protected $comment;

    public function __construct(\stdClass $entity, \stdClass $content)
    {
        Moment::setLocale('pl_PL');
        $this->populate($entity, $content);
    }

    /**
     * @inheritdoc
     */
    protected function populate(\stdClass $entity, \stdClass $content)
    {
        parent::populate($entity, $content);

        $this->checkIfPropertiesExists($this->entity, [
            'datetime',
            'publish',
            'event'],
            __CLASS__);

        $this->checkIfPropertiesExists($this->content, ['events'], __CLASS__);

        $this->date    = $this->entity->datetime;
        $this->unix    = strtotime($this->date);
        $this->comment = $this->entity->comment;

        $this->setProperties($entity, [
            'publish', 'b2Id', 'b2Info',
        ]);

    }

    /**
     * @return \Drupal\visualnet_api\Model\Hall
     */
    public function getHall()
    {
        return new Hall(
            $this->find($this->content->halls, $this->entity->hall),
            $this->content
        );
    }

    /**
     * @return \Drupal\visualnet_api\Model\Event
     */
    public function getEvent()
    {
        return new Event(
            $this->find($this->content->events, $this->entity->event),
            $this->content
        );
    }

    /**
     * @return \Drupal\visualnet_api\Model\B2Information
     */
    public function getB2Info()
    {
        if (is_null($this->b2Info)) {
            return null;
        }

        return new B2Information($this->b2Info, $this->content);
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
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
    public function getB2Id()
    {
        return $this->b2Id;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getButton()
    {
        $b2Information = $this->getB2Info();

        if (is_null($b2Information)) {
            return null;
        }

        $data = [
            'available_tickets' => $b2Information->getAvailableTickets(),
            'event_date'        => $this->getDate(),
            'publish'           => $this->isPublished(),
            'sale'              => $b2Information->getInfo()->sale->enabled,
            'end_sale'          => $b2Information->getInfo()->sale->endAt,
            'comment'           => $this->getComment(),
            'b2_id'             => $this->getB2Id(),
            'products'          => $b2Information->getInfo()->products,
        ];

        $button = new ButtonService($data);
        return $button->getButton();
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return (bool) $this->publish;
    }

    /**
     * @return bool
     */
    public function isBeforeEventDate()
    {
        $moment      = new Moment($this->date);
        $dateFromNow = $moment->fromNow();

        $diff = $dateFromNow->getSeconds();

        return ($diff < 0);
    }

}
