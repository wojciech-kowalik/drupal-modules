<?php

namespace Drupal\visualnet_content\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Visualnet crm event entities.
 *
 * @ingroup visualnet_content
 */
interface VisualnetCrmEventInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Visualnet crm event name.
   *
   * @return string
   *   Name of the Visualnet crm event.
   */
  public function getName();

  /**
   * Sets the Visualnet crm event name.
   *
   * @param string $name
   *   The Visualnet crm event name.
   *
   * @return \Drupal\visualnet_content\Entity\VisualnetCrmEventInterface
   *   The called Visualnet crm event entity.
   */
  public function setName($name);

  /**
   * Gets the Visualnet crm event creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Visualnet crm event.
   */
  public function getCreatedTime();

  /**
   * Sets the Visualnet crm event creation timestamp.
   *
   * @param int $timestamp
   *   The Visualnet crm event creation timestamp.
   *
   * @return \Drupal\visualnet_content\Entity\VisualnetCrmEventInterface
   *   The called Visualnet crm event entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Visualnet crm event published status indicator.
   *
   * Unpublished Visualnet crm event are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Visualnet crm event is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Visualnet crm event.
   *
   * @param bool $published
   *   TRUE to set this Visualnet crm event to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\visualnet_content\Entity\VisualnetCrmEventInterface
   *   The called Visualnet crm event entity.
   */
  public function setPublished($published);

}
