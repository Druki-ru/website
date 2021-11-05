<?php

namespace Drupal\druki_content\Data;

use Drupal\druki_content\Queue\ContentSyncQueueItemInterface;

/**
 * Provides queue item for clean up missing content.
 *
 * This queue item exists to be detected by queue processor. It doesn't have
 * any payload, because it just utility queue item used to clean up deleted
 * entities during synchronization.
 */
final class ContentSyncCleanQueueItem implements ContentSyncQueueItemInterface {

  /**
   * {@inheritdoc}
   */
  public function getPayload(): int {
    return 0;
  }

}
