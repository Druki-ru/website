<?php

namespace Drupal\druki_content\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the druki_content entity type.
 */
final class ContentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResultInterface {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view druki_content');

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, [
          'delete druki_content',
          'administer druki_content',
        ], 'OR');

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResultInterface {
    return AccessResult::allowedIfHasPermissions($account, [
      'create druki_content',
      'administer druki_content',
    ], 'OR');
  }

}
