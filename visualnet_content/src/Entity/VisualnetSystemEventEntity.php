<?php

namespace Drupal\visualnet_content\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the visualnet system event entity class.
 *
 * @ContentEntityType(
 *   id = "visualnet_system_event",
 *   label = @Translation("Visualnet System Event"),
 *   admin_permission = "administer visualnet_system_events",
 *   base_table = "visualnet_system_events",
 *   data_table = "visualnet_system_events_field_data",
 *   entity_keys = {
 *     "id" = "id",
 *     "system_event_id" = "system_event_id"
 *   }
 * )
 */
class VisualnetSystemEventEntity extends ContentEntityBase
{

    /**
     * @param $id
     *
     * @return $this
     */
    public function setSystemEventId($id)
    {
        $this->get('system_event_id')->value = $id;
        return $this;
    }

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->get('name')->value = $title;
        return $this;
    }

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitleOriginal($title)
    {
        $this->get('title_original')->value = $title;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of system event'))
            ->setReadOnly(true)
            ->setSetting('unsigned', true);

        $fields['system_event_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('System id'))
            ->setDescription(t('Visualnet event system id'))
            ->setReadOnly(true);

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Event title'))
            ->setRequired(true);

        $fields['title_original'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Event title'));

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        return $fields;
    }

}
