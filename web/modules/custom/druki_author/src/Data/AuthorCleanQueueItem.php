<?php

declare(strict_types=1);

namespace Drupal\druki_author\Data;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;

/**
 * Provides queue item for clean up authors.
 */
final class AuthorCleanQueueItem implements EntitySyncQueueItemInterface {

  /**
   * {@inheritdoc}
   */
  public function getPayload(): mixed {
    return NULL;
  }

}
