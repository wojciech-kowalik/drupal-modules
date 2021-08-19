<?php

namespace Drupal\visualnet_content\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Visualnet crm event entities.
 */
class VisualnetCrmEventViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
