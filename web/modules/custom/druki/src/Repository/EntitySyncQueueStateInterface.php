<?php

declare(strict_types=1);

namespace Drupal\druki\Repository;

/**
 * Provides an interface for queue state repositories used for entity sync.
 */
interface EntitySyncQueueStateInterface {

  /**
   * Adds processed entity IDs into storage.
   *
   * @param array $ids
   *   An array with entity IDs.
   */
  public function storeEntityIds(array $ids): void;

  /**
   * Gets stored entity IDs.
   *
   * @return array
   *   An array with entity IDs.
   */
  public function getEntityIds(): array;

  /**
   * Clears stored values.
   */
  public function delete(): void;

}
