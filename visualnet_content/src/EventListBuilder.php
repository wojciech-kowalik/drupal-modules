<?php

namespace Drupal\visualnet_content;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Link;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class to build a listing of Event entities.
 *
 * @ingroup visualnet_content
 */
class EventListBuilder extends EntityListBuilder
{

    /**
     * The number of entities to list per page, or FALSE to list all entities.
     *
     * For example, set this to FALSE if the list uses client-side filters that
     * require all entities to be listed (like the views overview).
     *
     * @var int|false
     */
    protected $limit = 5;

    /**
     * {@inheritdoc}
     */
    public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type)
    {
        return new static(
            $entity_type,
            $container->get('entity.manager')->getStorage($entity_type->id())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $query = $this->storage->getQuery();
        $query->pager($this->limit);
        $header = $this->buildHeader();
        $query->tableSort($header);
        $uids = $query->execute();
        return $this->storage->loadMultiple($uids);
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeader()
    {
        $header = [
            'id'       => [
                'data'      => $this->t('Event id', LanguageUtility::getCurrentLangCode()),
                'field'     => 'id',
                'specifier' => 'id',
            ],
            'name'     => [
                'data'      => $this->t('Name', LanguageUtility::getCurrentLangCode()),
                'field'     => 'name',
                'specifier' => 'name',
                'class'     => [RESPONSIVE_PRIORITY_LOW],
            ],
            'langcode' => [
                'data'      => $this->t('Language', LanguageUtility::getCurrentLangCode()),
                'field'     => 'langcode',
                'specifier' => 'langcode',
                'class'     => [RESPONSIVE_PRIORITY_LOW],
            ],
        ];

        return $header + parent::buildHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function buildRow(EntityInterface $entity)
    {
        /* @var $entity \Drupal\visualnet_content\Entity\Event */
        $row['id']   = $entity->id();
        $row['name'] = Link::createFromRoute(
            $entity->label(),
            'entity.visualnet_event.edit_form',
            ['visualnet_event' => $entity->id()]
        );
        $row['langcode'] = $entity->get('langcode')->value;

        return $row + parent::buildRow($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $build                    = parent::render();
        $build['table']['#empty'] = $this->t(
            'No events available',
            LanguageUtility::getCurrentLangCode()
        );
        return $build;
    }

}
