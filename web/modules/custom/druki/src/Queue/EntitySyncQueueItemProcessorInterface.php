<?php

declare(strict_types=1);

namespace Drupal\druki\Queue;

/**
 * Provides an interface for entity synchronization queue item processor.
 */
interface EntitySyncQueueItemProcessorInterface {

  /**
   * Check if current processor is suitable to processes provided queue item.
   *
   * @param \Drupal\druki\Queue\EntitySyncQueueItemInterface $item
   *   The queue item object.
   *
   * @return bool
   *   TRUE if processor can process it.
   */
  public function isApplicable(EntitySyncQueueItemInterface $item): bool;

  /**
   * Process the single queue item object.
   *
   * @param \Drupal\druki\Queue\EntitySyncQueueItemInterface $item
   *   The queue item object.
   *
   * @return array
   *   An array with IDs of created or updated entities. Returns an empty array
   *   if not applicable.
   */
  public function process(EntitySyncQueueItemInterface $item): array;

}
