<?php

namespace Drupal\visualnet_content\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\image\Entity\ImageStyle;
use Drupal\visualnet_api\Model\Event;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_content\Builder\EventQueryBuilder;
use Drupal\visualnet_content\Builder\EventQueryDirector;
use Drupal\visualnet_content\Entity\Event as EventEntity;
use Drupal\visualnet_content\Entity\VisualnetCrmEvent;
use Drupal\visualnet_content\Exception\VisualnetEventMakeException;
use Drupal\visualnet_utility\Service\UtilityService;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Drupal\visualnet_utility\Utility\ModuleUtility;

/**
 * Class EventService
 *
 * @package Drupal\visualnet_content\Service
 */
class EventService
{
    const IMAGE_LIST_STYLE  = 'node_teaser_image_style';
    const IMAGE_EVENT_STYLE = 'event_image_style';

    /**
     * @var Drupal\Core\Entity\EntityTypeManager
     */
    private $entityManager;

    /**
     * @var \Drupal\visualnet_utility\Service\UtilityService
     */
    private $utility;

    /**
     * @var \Drupal\Core\Database\Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $language;

    /**
     * EventService constructor.
     *
     * @param \Drupal\Core\Entity\EntityTypeManager            $entityManager
     * @param \Drupal\Core\Database\Connection                 $connection
     * @param \Drupal\visualnet_utility\Service\UtilityService $utility
     */
    public function __construct(
        EntityTypeManager $entityManager,
        Connection $connection,
        UtilityService $utility
    ) {

        $this->connection    = $connection;
        $this->utility       = $utility;
        $this->entityManager = $entityManager;
        $this->language      = LanguageUtility::getCurrentLangCode();

    }

    /**
     * @throws \Drupal\visualnet_api\Exception\NotSupportedRequestException
     * @throws \Drupal\visualnet_content\Exception\VisualnetEventMakeException
     */
    public function saveEvents()
    {
        $events = $this->utility->getCollectionByType(RequestType::EVENT);

        foreach ($events as $event) {
            $this->makeCrmEvent($event);
        }

    }

    /**
     * @param $id
     *
     * @return \Drupal\Core\Entity\EntityInterface|null|static
     */
    public function getEventByCrmId($id)
    {
        $currentLangcode = LanguageUtility::getCurrentLangCode()['langcode'];

        $query = $this->connection->select('visualnet_events_field_data', 'q1');
        $query->innerJoin('visualnet_crm_events_field_data', 'q2', 'q1.crm_event_id = q2.id');
        $query->fields('q1', ['id']);
        $query->condition('q2.system_id', $id);
        $result = $query->execute()->fetchObject();

        $event = EventEntity::load($result->id);

        if (is_null($event)) {
            return null;
        }

        // searching for event in proper language
        if ($currentLangcode !== $event->get('langcode')->value) {

            $query = $this->connection->select('visualnet_events_field_data', 'q1');
            $query->fields('q1', ['id']);
            $query->condition('q1.langcode', $currentLangcode);
            $query->condition('q1.name_original', $event->get('name_original')->value);
            $result = $query->execute()->fetchObject();
            $event  = EventEntity::load($result->id);
        }

        return $event;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        $collection = [];
        $query      = $this->connection->select('visualnet_events_field_data', 'q1');
        $query->innerJoin('visualnet_crm_events_field_data', 'q2', 'q1.crm_event_id = q2.id');
        $query->fields('q1', ['id']);
        $events = $query->execute()->fetchAll();

        foreach ($events as $event) {

            $event        = EventEntity::load($event->id);
            $sectionTags  = $event->get('event_section')->referencedEntities();
            $crmEvent     = $event->get('crm_event_id')->referencedEntities();
            $eventId      = null;
            $sectionName  = null;
            $sectionColor = null;

            if ($crmEvent) {
                $eventId = $crmEvent[0]->get('system_id')->value;
            }

            if ($sectionTags) {
                $sectionName  = $sectionTags[0]->get('name')->value;
                $sectionColor = $sectionTags[0]->get('field_color')->color;
            }

            array_push($collection, [
                'id'             => $event->get('id')->value,
                'title'          => $event->get('name')->value,
                'title_original' => $event->get('name_original')->value,
                'crm_id'         => $eventId,
                'section'        => [
                    'name'  => $sectionName,
                    'color' => $sectionColor,
                ],
            ]);

            unset($event);

        }

        return $collection;

    }

    /**
     * @param array $options
     * @return array
     */
    public function loadMultiple(array $options)
    {
        $mapped             = null;
        $eventQueryDirector = new EventQueryDirector();
        $eventQueryBuilder  = new EventQueryBuilder($this->connection);

        $eventQueryDirector->setBuilder($eventQueryBuilder);
        $eventQueryDirector->setOptions($options);
        $eventQueryDirector->buildQuery();

        try {
            $mapped = $this->eventMapper($eventQueryBuilder->getQuery()->execute());
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $e) {
        }

        return $mapped;
    }

    /**
     * @return string
     */
    public function getLanguageToUrl()
    {
        $current = $this->language['langcode'];

        if ('en' == $current) {
            return '/' . $current;
        }
    }

    /**
     * @param \Drupal\visualnet_api\Model\Event $event
     *
     * @return \Drupal\Core\Entity\EntityInterface|null|static
     * @throws \Drupal\visualnet_content\Exception\VisualnetEventMakeException
     */
    private function makeCrmEvent(Event $event)
    {
        $crmEvent = $this->getCrmEventData($event->getId());

        if (!$crmEvent) {
            $crmEvent = VisualnetCrmEvent::create();
        } else {
            $crmEvent = VisualnetCrmEvent::load($crmEvent->id);
        }

        try {

            $crmEvent->setName($event->getTitle());
            $crmEvent->setSystemId($event->getId());
            $crmEvent->setPublished(true);
            $crmEvent->save();

        } catch (\Exception $e) {

            ModuleUtility::exceptionSupport($e);

            throw new VisualnetEventMakeException(
                $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $crmEvent;
    }

    /**
     * @param array $data
     * @return array
     */
    private function eventMapper($data)
    {
        $events = [];

        foreach ($data as $event) {
            array_push($events, [
                'id'              => $event->id,
                'title'           => $event->event_name,
                'title_original'  => $event->name_original,
                'description'     => $event->description,
                'year'            => $event->production_year,
                'start_date'      => $event->event_start_date,
                'duration'        => $event->duration,
                'langcode'        => $event->langcode,
                'type'            => $event->event_type,
                'section'         => $event->event_section,
                'image'           => $this->makeImageData($event, self::IMAGE_LIST_STYLE),
                'language_prefix' => $this->getLanguageToUrl(),
                'director'        => [
                    'name'        => $event->director__value,
                    'description' => $event->director_description__value,
                ],
            ]);
        }

        return $events;
    }

    /**
     * @param $entity
     *
     * @return array
     */
    public function makeImageData($entity, $style)
    {
        if ($entity->image_id) {

            return [
                'src' => ImageStyle::load($style)->buildUrl($entity->image_uri),
                'alt' => $entity->image_alt,
            ];

        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function getCrmEventData($id)
    {
        $query = $this->connection->select('visualnet_crm_events_field_data', 'q');
        $query->fields('q');
        $query->condition('q.system_id', $id);

        return $query->execute()->fetchObject();
    }

}
