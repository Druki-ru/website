<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

/**
 * Provides interface for redirect sync queue item processors.
 */
interface RedirectSyncQueueItemProcessorInterface {

  /**
   * Check if current processor is suitable to processes provided queue item.
   *
   * @param \Drupal\druki_redirect\Queue\RedirectSyncQueueItemInterface $item
   *   The queue item object.
   *
   * @return bool
   *   TRUE if processor can process it.
   */
  public function isApplicable(RedirectSyncQueueItemInterface $item): bool;

  /**
   * Process the single queue item object.
   *
   * @param \Drupal\druki_redirect\Queue\RedirectSyncQueueItemInterface $item
   *   The queue item object.
   *
   * @return array
   *   An array with IDs of created or updated redirects. Returns an empty array
   *   if not applicable.
   */
  public function process(RedirectSyncQueueItemInterface $item): array;

}
