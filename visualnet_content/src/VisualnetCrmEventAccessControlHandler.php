<?php

namespace Drupal\visualnet_content;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Visualnet crm event entity.
 *
 * @see \Drupal\visualnet_content\Entity\VisualnetCrmEvent.
 */
class VisualnetCrmEventAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\visualnet_content\Entity\VisualnetCrmEventInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished visualnet crm event entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published visualnet crm event entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit visualnet crm event entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete visualnet crm event entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add visualnet crm event entities');
  }

}
