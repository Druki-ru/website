<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Data;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;

/**
 * Provides redirect clean queue item.
 */
final class RedirectCleanQueueItem implements EntitySyncQueueItemInterface {

  /**
   * {@inheritdoc}
   */
  public function getPayload(): mixed {
    return NULL;
  }

}
