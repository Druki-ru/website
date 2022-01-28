<?php

namespace Drupal\Tests\druki\Traits;

/**
 * A helper to clean up created entities.
 *
 * @see https://gitlab.com/weitzman/drupal-test-traits/-/issues/54
 */
trait EntityCleanupTrait {

  /**
   * Entity types to clean up.
   *
   * @var array
   */
  private array $entityTypeIds = [];

  /**
   * Array of entity IDs keyed by entity type.
   *
   * @var array
   */
  private array $entityIds = [];

  /**
   * Store entity IDs.
   */
  protected function storeEntityIds(array $entity_type_ids): void {
    $this->entityTypeIds = $entity_type_ids;
    foreach ($this->entityTypeIds as $entity_type_id) {
      $this->entityIds[$entity_type_id] = self::getEntityIds($entity_type_id);
    }
  }

  /**
   * Cleans up entities created during the test.
   */
  protected function cleanupEntities(): void {
    foreach ($this->entityTypeIds as $entity_type_id) {
      $ids = self::getEntityIds($entity_type_id);
      $new_ids = \array_diff($ids, $this->entityIds[$entity_type_id]);
      self::removeEntities($entity_type_id, $new_ids);
    }
  }

  /**
   * Returns all entity IDs for a given entity type.
   */
  private static function getEntityIds(string $entity_type_id): array {
    return \Drupal::entityTypeManager()
      ->getStorage($entity_type_id)
      ->getQuery()
      ->execute();
  }

  /**
   * Removes specifies entities.
   */
  private static function removeEntities(string $entity_type_id, array $ids): void {
    $storage = \Drupal::entityTypeManager()->getStorage($entity_type_id);
    foreach ($storage->loadMultiple($ids) as $entity) {
      $entity->delete();
    }
  }

}
