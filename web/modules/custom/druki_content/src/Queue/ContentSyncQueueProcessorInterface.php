<?php

namespace Drupal\druki_content\Queue;

/**
 * Provides interface for queue item processors.
 */
interface ContentSyncQueueProcessorInterface {

  /**
   * Process provides queue item.
   *
   * @param \Drupal\druki_content\Queue\ContentSyncQueueItemInterface $item
   *   The queue item to process.
   *
   * @return array
   *   An array with IDs of created or updated entities. Returns an empty array
   *   if not applicable.
   */
  public function process(ContentSyncQueueItemInterface $item): array;

  /**
   * Checks if provided item can be processed by current processor.
   *
   * @param \Drupal\druki_content\Queue\ContentSyncQueueItemInterface $item
   *   The queue item to check.
   *
   * @return bool
   *   TRUE if can be processed, FALSE otherwise.
   */
  public function isApplicable(ContentSyncQueueItemInterface $item): bool;

}
