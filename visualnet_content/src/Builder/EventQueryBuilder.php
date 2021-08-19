<?php

namespace Drupal\visualnet_content\Builder;

use Assert\Assertion;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Database\Driver\mysql\Select;
use Drupal\visualnet_api\Model\Event;
use Drupal\visualnet_content\Builder\QueryBuilderInterface;
use Drupal\visualnet_content\Entity\Event as EventEntity;

/**
 * Class EventQueryBuilder
 *
 * @package Drupal\visualnet_content\Builder
 */
class EventQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var Drupal\Core\Database\Driver\mysql\Select
     */
    private $query;

    /**
     * @return void
     */
    protected function reset()
    {
        $this->query = new \stdClass();
    }

    /**
     * @param \Drupal\Core\Database\Driver\mysql\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->reset();
        $this->query = $connection->select('visualnet_events_field_data', 'vefd');
    }

    /**
     * @inheritDoc
     */
    public function addJoins()
    {
        $this->query->leftJoin('taxonomy_term_field_data', 'ttfd1', 'ttfd1.tid = vefd.event_type');
        $this->query->leftJoin('taxonomy_term_field_data', 'ttfd2', 'ttfd2.tid = vefd.event_section');
        $this->query->leftJoin('file_managed', 'fm', 'fm.fid = vefd.image__target_id');
    }

    /**
     * @inheritDoc
     */
    public function addFields()
    {
        $this->query->fields('vefd');
        $this->query->fields('ttfd1');
        $this->query->fields('ttfd2');
        $this->query->fields('fm');
        $this->query->addField('vefd', 'name', 'event_name');
        $this->query->addField('vefd', 'description__value', 'description');
        $this->query->addField('ttfd1', 'name', 'event_type');
        $this->query->addField('ttfd2', 'name', 'event_section');
        $this->query->addField('vefd', 'image__target_id', 'image_id');
        $this->query->addField('vefd', 'image__alt', 'image_alt');
        $this->query->addField('fm', 'uri', 'image_uri');
    }

    /**
     * @inheritDoc
     */
    public function addConditions(array $options)
    {
        if (isset($options['langcode'])) {
            $this->query->condition('vefd.langcode', $options['langcode']);
        }

        if (isset($options['name'])) {
            $group = $this->query->orConditionGroup()
                ->condition('vefd.description__value', "%" . $options['name'] . "%", 'LIKE')
                ->condition('vefd.name_original', "%" . $options['name'] . "%", 'LIKE')
                ->condition('vefd.name', "%" . $options['name'] . "%", 'LIKE');

            $this->query->condition($group);
        }

        if (isset($options['event_type'])) {

            if (EventEntity::ALL_EVENT_TYPE == $options['event_type']) {
                $this->query->condition('ttfd1.name', EventEntity::getAllDefaultEventTypes(), 'IN');
            } else {
                $this->query->condition('ttfd1.name', $options['event_type']);
            }

        }
    }

    /**
     * @inheritDoc
     */
    public function addOrders(array $options)
    {
        $customSections = ['section_name'];

        if (in_array($options['field'], $customSections) && isset($options['dir'])) {
            $this->query->orderBy('ttfd2.name', mb_strtoupper($options['dir']));
            return;
        }

        if (isset($options['field']) && isset($options['dir'])) {
            Assertion::inArray($options['dir'], ['desc', 'asc']);
            $this->query->orderBy('vefd.' . $options['field'], mb_strtoupper($options['dir']));
            return;
        }

        $this->query->orderBy('vefd.created', 'DESC');
    }

    /**
     * @return \Drupal\Core\Database\Driver\mysql\Select
     */
    public function getQuery(): Select
    {
        return $this->query;
    }

}
