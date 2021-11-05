<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

/**
 * Provides interface for redirect sync queue items.
 */
interface RedirectSyncQueueItemInterface {

  /**
   * Gets queue item payload.
   *
   * @return mixed
   *   The payload value.
   */
  public function getPayload(): mixed;

}
