<?php

namespace Drupal\druki_content\Queue;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;

/**
 * Provides interface for queue item processors.
 */
interface ContentSyncQueueProcessorInterface {

  /**
   * Process provides queue item.
   *
   * @param \Drupal\druki\Queue\EntitySyncQueueItemInterface $item
   *   The queue item to process.
   *
   * @return array
   *   An array with IDs of created or updated entities. Returns an empty array
   *   if not applicable.
   */
  public function process(EntitySyncQueueItemInterface $item): array;

  /**
   * Checks if provided item can be processed by current processor.
   *
   * @param \Drupal\druki\Queue\EntitySyncQueueItemInterface $item
   *   The queue item to check.
   *
   * @return bool
   *   TRUE if can be processed, FALSE otherwise.
   */
  public function isApplicable(EntitySyncQueueItemInterface $item): bool;

}
