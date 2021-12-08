<?php

declare(strict_types=1);

namespace Drupal\druki\Queue;

/**
 * Provides an interface for entity synchronization queue items.
 */
interface EntitySyncQueueItemInterface {

  /**
   * Gets queue item payload.
   *
   * @return mixed
   *   The payload for queue.
   */
  public function getPayload(): mixed;

}
