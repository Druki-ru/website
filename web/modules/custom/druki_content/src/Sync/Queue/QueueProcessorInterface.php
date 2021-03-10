<?php

namespace Drupal\druki_content\Sync\Queue;

/**
 * Provides interface for queue item processors.
 */
interface QueueProcessorInterface {

  /**
   * Process provides queue item.
   *
   * @param \Drupal\druki_content\Sync\Queue\QueueItemInterface $item
   *   The queue item to process.
   */
  public function process(QueueItemInterface $item): void;

  /**
   * Checks if provided item can be processed by current processor.
   *
   * @param \Drupal\druki_content\Sync\Queue\QueueItemInterface $item
   *   The queue item to check.
   *
   * @return bool
   *   TRUE if can be processed, FALSE otherwise.
   */
  public function isApplicable(QueueItemInterface $item): bool;

}
