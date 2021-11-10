<?php

declare(strict_types=1);

namespace Drupal\druki_author\Queue;

/**
 * Provides an interface for author synchronization queue item.
 */
interface AuthorSyncQueueItemInterface {

  /**
   * Gets queue item payload.
   *
   * @return mixed
   *   The payload value.
   */
  public function getPayload(): mixed;

}
