<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Data;

use Drupal\druki_redirect\Queue\RedirectSyncQueueItemInterface;

/**
 * Provides redirect clean queue item.
 */
final class RedirectCleanQueueItem implements RedirectSyncQueueItemInterface {

  /**
   * {@inheritdoc}
   */
  public function getPayload(): mixed {
    return NULL;
  }

}
