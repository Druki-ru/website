<?php

declare(strict_types=1);

namespace Drupal\druki_author\Data;

use Drupal\druki_author\Queue\AuthorSyncQueueItemInterface;

/**
 * Provides queue item for clean up authors.
 */
final class AuthorCleanQueueItem implements AuthorSyncQueueItemInterface {

  /**
   * {@inheritdoc}
   */
  public function getPayload(): mixed {
    return NULL;
  }

}
