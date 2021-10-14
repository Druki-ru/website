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
   */
  public function process(ContentSyncQueueItemInterface $item): void;

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
