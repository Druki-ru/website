<?php

namespace Drupal\druki_content\Queue;

/**
 * Provides interface for queue items used during content synchronization.
 */
interface ContentSyncQueueItemInterface {

  /**
   * Gets queue item payload.
   *
   * @return mixed
   *   The payload for queue.
   */
  public function getPayload();

}
