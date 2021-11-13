<?php

declare(strict_types=1);

namespace Drupal\druki\Factory;

use Drupal\druki\Repository\EntitySyncQueueStateInterface;

/**
 * Defines an interface for entity sync queue state factories.
 */
interface EntitySyncQueueStateFactoryInterface {

  /**
   * Gets instance of entity sync queue state repository.
   *
   * @param string $storage_key
   *   The storage key used by a repository.
   *
   * @return \Drupal\druki\Repository\EntitySyncQueueStateInterface
   *   The repository instance.
   */
  public function get(string $storage_key): EntitySyncQueueStateInterface;

}
