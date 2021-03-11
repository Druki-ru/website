<?php

namespace Drupal\druki_content\Sync\Queue;

/**
 * Provides interface for queue items used during content synchronization.
 */
interface QueueItemInterface {

  /**
   * Gets queue item payload.
   *
   * @return mixed
   *   The payload for queue.
   */
  public function getPayload();

}
