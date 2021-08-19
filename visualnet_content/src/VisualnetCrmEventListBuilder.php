<?php

namespace Drupal\visualnet_content;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Visualnet crm event entities.
 *
 * @ingroup visualnet_content
 */
class VisualnetCrmEventListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Visualnet crm event ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\visualnet_content\Entity\VisualnetCrmEvent */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.visualnet_crm_event.edit_form',
      ['visualnet_crm_event' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
