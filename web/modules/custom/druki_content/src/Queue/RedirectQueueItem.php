<?php

namespace Drupal\druki_content\Queue;

use Drupal\druki_content\Data\RedirectSourceFileList;
use Drupal\druki_content\Sync\Queue\QueueItemInterface;

/**
 * Provides queue item with redirects.
 */
final class RedirectQueueItem implements QueueItemInterface {

  /**
   * The redirect file list.
   */
  protected RedirectSourceFileList $payload;

  /**
   * RedirectQueueItem constructor.
   *
   * @param \Drupal\druki_content\Data\RedirectSourceFileList $payload
   *   The redirect file list.
   */
  public function __construct(RedirectSourceFileList $payload) {
    $this->payload = $payload;
  }

  /**
   * {@inheritdoc}
   */
  public function getPayload(): RedirectSourceFileList {
    return $this->payload;
  }

}
