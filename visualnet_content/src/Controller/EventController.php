<?php

namespace Drupal\visualnet_content\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\visualnet_api\Comparer\UnixDateComparer;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_content\Entity\Event;
use Drupal\visualnet_content\Service\EventService;
use Drupal\visualnet_utility\Utility\LanguageUtility;

/**
 * Class EventController
 *
 * @package   Drupal\visualnet_content\Controller
 * @access    public
 * @copyright visualnet.pl
 */
class EventController extends ControllerBase
{
    /**
     * @var Drupal\Core\Entity\EntityStorageInterface|object
     */
    protected $eventStorage;

    /**
     * @var Drupal\Core\Entity\EntityStorageInterface|object
     */
    protected $taxonomyStorage;

    /**
     * @var string
     */
    protected $currentUrl;

    /**
     * @var \Drupal\Core\Config\Config
     */
    protected $eventConfig;

    /**
     * @var mixed
     */
    protected $utilityService;

    /**
     * @var mixed
     */
    protected $eventService;

    /**
     * @var mixed
     */
    protected $database;

    /**
     * EventController constructor.
     */
    public function __construct()
    {
        $currentPath = Drupal::service('path.current')->getPath();

        $this->eventConfig     = $this->config('event.settings');
        $this->eventStorage    = $this->entityTypeManager()->getStorage("visualnet_event");
        $this->taxonomyStorage = $this->entityTypeManager()->getStorage("taxonomy_term");
        $this->currentUrl      = Drupal::service('path.alias_manager')->getAliasByPath($currentPath);
        $this->utilityService  = Drupal::service('visualnet.utility_service');
        $this->eventService    = Drupal::service('visualnet.event_service');
        $this->database        = Drupal::service('database');
    }

    /**
     * @param $eventType
     * @return array
     */
    public function event($eventType)
    {
        $options  = [];
        $entities = [];

        $options['event_type'] = $eventType;
        $options['langcode']   = LanguageUtility::getCurrentLangCode()['langcode'];
        $options['name']       = Drupal::request()->query->get('name');
        $options['field']      = Drupal::request()->query->get('field');
        $options['dir']        = Drupal::request()->query->get('dir');

        $events = $this->eventService->loadMultiple($options);

        if (Event::MOVIE_EVENT_TYPE == $eventType && !is_null($events)) {
            $entities = $this->groupEventsBySection($events);
        } else {
            $entities = $events;
        }

        return array(
            '#entities'   => $entities,
            '#type'       => $eventType,
            '#options'    => $options,
            '#sort_field' => $options['field'],
            '#theme'      => 'event-view',
            '#cache'      => ['max-age' => 0],
        );
    }

    /**
     * @param $eventId
     *
     * @return array
     */
    public function getEvent($eventId)
    {
        $event  = $this->eventStorage->load($eventId);
        $entity = $this->getEventData($event);

        return array(
            '#event'    => $entity,
            '#theme'    => 'event',
            '#cache'    => [
                'max-age' => 0,
            ],
            '#attached' => [
                'library' => [
                    'visualnet_content/responsive-styling',
                ],
            ],
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function populateCrmEvents()
    {
        $this->eventService->saveEvents();

        drupal_set_message(t('Data has been downloaded', LanguageUtility::getCurrentLangCode()));
        return $this->redirect('<front>');
    }

    /**
     * @param \Drupal\visualnet_content\Entity\Event $event
     *
     * @return array
     */
    public function getEventData(Event $event)
    {
        $crmEvent    = $event->get('crm_event_id')->referencedEntities();
        $typeTags    = $event->get('event_type')->referencedEntities();
        $sectionTags = $event->get('event_section')->referencedEntities();

        $typeTagName     = false;
        $sectionTagName  = false;
        $sectionTagColor = false;

        $repertoires = [];
        $eventId     = false;

        if ($crmEvent) {

            $eventId     = $crmEvent[0]->get('system_id')->value;
            $repertoires = $this->getEventRepertoires($eventId);

        }

        if (!is_null($typeTags)) {

            $typeTag     = $typeTags[0];
            $typeTagName = $typeTag->get('name')->value;
        }

        if (!is_null($sectionTags)) {

            if (isset($sectionTags[0])) {
                $sectionTag      = $sectionTags[0];
                $sectionTagName  = $sectionTag->get('name')->value;
                $sectionTagColor = $sectionTag->get('field_color')->color;
            }
        }

        $eventImageData            = new \stdClass();
        $eventImageData->image_id  = $event->get('image')->entity->id();
        $eventImageData->image_uri = $event->get('image')->entity->uri->value;
        $eventImageData->image_alt = $event->get('image')->alt;

        return [
            'id'             => $event->get('id')->value,
            'title'          => $event->get('name')->value,
            'title_original' => $event->get('name_original')->value,
            'description'    => $event->get('description')->value,
            'year'           => $event->get('production_year')->value,
            'duration'       => $event->get('duration')->value,
            'screenplay'     => $event->get('screenplay')->value,
            'pictures'       => $event->get('pictures')->value,
            'editor'         => $event->get('editor')->value,
            'composer'       => $event->get('music_composer')->value,
            'cast'           => $event->get('cast')->value,
            'producer'       => $event->get('producer')->value,
            'production'     => $event->get('production')->value,
            'event_language' => $event->get('event_language')->value,
            'color'          => $event->get('color')->value,
            'prize'          => $event->get('prize')->value,
            'director'       => [
                'name'        => $event->get('director')->value,
                'description' => $event->get('director_description')->value,
            ],
            'type'           => $typeTagName,
            'section'        => [
                'name'  => $sectionTagName,
                'color' => $sectionTagColor,
            ],
            'image'          => $this->eventService->makeImageData($eventImageData, EventService::IMAGE_EVENT_STYLE),
            'crm_id'         => $eventId,
            'repertoires'    => $repertoires,
        ];
    }

    /**
     * @param array $events
     * @return array
     */
    private function groupEventsBySection(array $events)
    {
        $entities        = [];
        $eventsInSection = $this->eventConfig->get("visualnet_content_events_in_section");
        $sections        = $this->taxonomyStorage->loadTree('event_sections');

        foreach ($sections as $section) {

            $selectedEvents = [];
            $eventCount     = 1;

            foreach ($events as $event) {

                if ($event['section'] == $section->name && $eventCount <= $eventsInSection) {
                    $eventCount += 1;
                    $selectedEvents[] = $event;
                }
            }

            $entities[] = [
                'name'            => $section->name,
                'id'              => $section->tid,
                'color'           => $this->taxonomyStorage->load($section->tid)->get('field_color')->color,
                'language_prefix' => $this->eventService->getLanguageToUrl(),
                'events'          => $selectedEvents,
            ];
        }

        return $entities;
    }

    /**
     * @param $id
     *
     * @return Collections\Vector
     */
    private function getEventRepertoires($id)
    {
        $repertoires = $this->utilityService->getCollectionByType(
            RequestType::REPERTOIRE,
            ['event' => $id]
        );

        $repertoires->sort(new UnixDateComparer());

        return $repertoires;

    }

}
