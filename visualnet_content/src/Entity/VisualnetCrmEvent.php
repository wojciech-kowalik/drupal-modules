<?php

namespace Drupal\visualnet_content\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the Visualnet crm event entity.
 *
 * @ingroup visualnet_content
 *
 * @ContentEntityType(
 *   id = "visualnet_crm_event",
 *   label = @Translation("Visualnet crm event"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\visualnet_content\VisualnetCrmEventListBuilder",
 *     "views_data" = "Drupal\visualnet_content\Entity\VisualnetCrmEventViewsData",
 *     "translation" = "Drupal\visualnet_content\VisualnetCrmEventTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\visualnet_content\Form\VisualnetCrmEventForm",
 *       "add" = "Drupal\visualnet_content\Form\VisualnetCrmEventForm",
 *       "edit" = "Drupal\visualnet_content\Form\VisualnetCrmEventForm",
 *       "delete" = "Drupal\visualnet_content\Form\VisualnetCrmEventDeleteForm",
 *     },
 *     "access" = "Drupal\visualnet_content\VisualnetCrmEventAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\visualnet_content\VisualnetCrmEventHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "visualnet_crm_events",
 *   data_table = "visualnet_crm_events_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer visualnet crm event entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/visualnet_crm_event/{visualnet_crm_event}",
 *     "add-form" = "/admin/structure/visualnet_crm_event/add",
 *     "edit-form" = "/admin/structure/visualnet_crm_event/{visualnet_crm_event}/edit",
 *     "delete-form" = "/admin/structure/visualnet_crm_event/{visualnet_crm_event}/delete",
 *     "collection" = "/admin/structure/visualnet_crm_event",
 *   },
 *   field_ui_base_route = "visualnet_crm_event.settings"
 * )
 */
class VisualnetCrmEvent extends ContentEntityBase implements VisualnetCrmEventInterface
{

    use EntityChangedTrait;

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
     * {@inheritdoc}
     */
    public function getSystemId()
    {
        return $this->get('system_id')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setSystemId($id)
    {
        $this->set('system_id', $id);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get('name')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->set('name', $name);
        return $this;
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

        $fields['system_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('System event id'))
            ->setSettings([
                'max_length'      => 30,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'integer',
                'weight' => -5,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'integer',
                'weight' => -5,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Name'))
            ->setDescription(t('The name of the Visualnet crm event entity.'))
            ->setSettings([
                'max_length'      => 50,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label'  => 'above',
                'type'   => 'string',
                'weight' => -4,
            ])
            ->setDisplayOptions('form', [
                'type'   => 'string_textfield',
                'weight' => -4,
            ])
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true)
            ->setRequired(true);

        $fields['status'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('Publishing status'))
            ->setDescription(t('A boolean indicating whether the Visualnet crm event is published.'))
            ->setDefaultValue(true)
            ->setDisplayOptions('form', [
                'type'   => 'boolean_checkbox',
                'weight' => -3,
            ]);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Authored by'))
            ->setDescription(t('The user ID of author of the Visualnet crm event entity.'))
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

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));

        return $fields;
    }

}
