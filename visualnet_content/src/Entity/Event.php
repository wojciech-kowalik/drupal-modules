<?php

namespace Drupal\visualnet_content\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the Event entity.
 *
 * @ingroup visualnet_content
 *
 * @ContentEntityType(
 *   id = "visualnet_event",
 *   label = @Translation("Event"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\visualnet_content\EventListBuilder",
 *     "views_data" = "Drupal\visualnet_content\Entity\EventViewsData",
 *     "translation" = "Drupal\visualnet_content\EventTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\visualnet_content\Form\EventForm",
 *       "add" = "Drupal\visualnet_content\Form\EventForm",
 *       "edit" = "Drupal\visualnet_content\Form\EventForm",
 *       "delete" = "Drupal\visualnet_content\Form\EventDeleteForm",
 *     },
 *     "access" = "Drupal\visualnet_content\EventAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\visualnet_content\EventHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "visualnet_events",
 *   data_table = "visualnet_events_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer event entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/event/show/{visualnet_event}",
 *     "add-form" = "/admin/config/content/event/add",
 *     "edit-form" = "/admin/config/content/event/{visualnet_event}/edit",
 *     "delete-form" = "/admin/config/content/event/{visualnet_event}/delete",
 *     "collection" = "/admin/config/content/event",
 *   },
 *   field_ui_base_route = "visualnet_event.settings"
 * )
 */
class Event extends ContentEntityBase implements EventInterface
{

    use EntityChangedTrait;

    /**
     * @var const
     */
    const MOVIE_EVENT_TYPE        = 'movie';
    const MEETING_EVENT_TYPE      = 'meeting';
    const LECTURE_EVENT_TYPE      = 'lecture';
    const SPECIAL_SHOW_EVENT_TYPE = 'special-show';
    const ALL_EVENT_TYPE          = 'all';

    /**
     * {@inheritdoc}
     */
    public static function preCreate(EntityStorageInterface $storage_controller, array &$values)
    {
        parent::preCreate($storage_controller, $values);
        $values += [
            'user_id' => \Drupal::currentUser()->id(),
        ];
    }

    /**
     * @return array
     */
    public static function getAllDefaultEventTypes()
    {
        return [
            self::MEETING_EVENT_TYPE,
            self::LECTURE_EVENT_TYPE,
            self::SPECIAL_SHOW_EVENT_TYPE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->get('created')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedTime($timestamp)
    {
        $this->set('created', $timestamp);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->get('user_id')->entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerId()
    {
        return $this->get('user_id')->target_id;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerId($uid)
    {
        $this->set('user_id', $uid);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(UserInterface $account)
    {
        $this->set('user_id', $account->id());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return (bool) $this->getEntityKey('status');
    }

    /**
     * {@inheritdoc}
     */
    public function setPublished($published)
    {
        $this->set('status', $published ? true : false);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['crm_event_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('CRM event id'))
            ->setDescription(t('Please select event from external crm system'))
            ->setSetting('target_type', 'visualnet_crm_event')
            ->setSetting('handler', 'default')
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'string',
                'weight' => -9,
            ])
            ->setDisplayOptions('form', [
                'type'     => 'entity_reference_autocomplete',
                'weight'   => -9,
                'settings' => [
                    'match_operator'    => 'CONTAINS',
                    'size'              => '60',
                    'autocomplete_type' => 'tags',
                    'placeholder'       => '',
                ],
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Authored by'))
            ->setRevisionable(true)
            ->setSetting('target_type', 'user')
            ->setSetting('handler', 'default')
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'author',
                'weight' => 0,
            ])
            ->setDisplayOptions('form', [
                'type'     => 'entity_reference_autocomplete',
                'weight'   => 5,
                'settings' => [
                    'match_operator'    => 'CONTAINS',
                    'size'              => '60',
                    'autocomplete_type' => 'tags',
                    'placeholder'       => '',
                ],
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['event_type'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Event Type'))
            ->setDescription(t('Event Type tag'))
            ->setSetting('target_type', 'taxonomy_term')
            ->setSetting('handler', 'default:taxonomy_term')
            ->setSetting('handler_settings', [
                'target_bundles' => [
                    'event_types' => 'event_types',
                ],
            ])
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'string',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'select',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['event_section'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Event Section'))
            ->setDescription(t('Event Section tag'))
            ->setSetting('target_type', 'taxonomy_term')
            ->setSetting('handler', 'default:taxonomy_term')
            ->setSetting('handler_settings', [
                'target_bundles' => [
                    'event_sections' => 'event_sections',
                ],
            ])
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'string',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'select',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['event_start_date'] = BaseFieldDefinition::create('timestamp')
            ->setLabel(t('Start event date'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setRequired(false);

        $fields['image'] = BaseFieldDefinition::create('image')
            ->setLabel(t('Event Image'))
            ->setDescription(t('Event Image file'))
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'image',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'image',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true)
            ->setRequired(true);

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Title'))
            ->setSettings([
                'max_length'      => 128,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'string',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'string_textfield',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['name_original'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Title original'))
            ->setSettings([
                'max_length'      => 128,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'string',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'string_textfield',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['description'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Description event'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -5,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -5,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['production_year'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Production year'))
            ->setSettings([
                'max_length'      => 30,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'integer',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'integer',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['duration'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Duration of the event'))
            ->setSettings([
                'max_length'      => 30,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'integer',
                'weight' => -3,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'integer',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['director'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Director'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['director_description'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Director description'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['screenplay'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Screenplay'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['pictures'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Pictures'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['editor'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Editor'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['music_composer'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Music'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['cast'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Cast'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['producer'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Producer'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['production'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Production'))
            ->setTranslatable(true)
            ->setDisplayOptions('view', [
                'label'  => 'hidden',
                'type'   => 'text_default',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('view', true)
            ->setDisplayOptions('form', [
                'type'   => 'text_textfield',
                'weight' => -3,
            ])
            ->setDisplayConfigurable('form', true);

        $fields['event_language'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Language'))
            ->setSettings([
                'max_length'      => 50,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'string',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'string_textfield',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['color'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Color'))
            ->setSettings([
                'max_length'      => 50,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'string',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'string_textfield',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['prize'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Prize'))
            ->setSettings([
                'max_length'      => 255,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'string',
                'weight' => -6,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'string_textfield',
                'weight' => -6,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['status'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('Publishing status'))
            ->setDescription(t('A boolean indicating whether the Event is published'))
            ->setDefaultValue(true);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited'));

        return $fields;
    }

}
