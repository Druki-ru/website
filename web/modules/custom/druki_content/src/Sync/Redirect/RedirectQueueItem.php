<?php

namespace Drupal\druki_content\Sync\Redirect;

use Drupal\druki_content\Sync\Queue\QueueItemInterface;

/**
 * Provides queue item with redirects.
 */
final class RedirectQueueItem implements QueueItemInterface {

  /**
   * The redirect file list.
   *
   * @var \Drupal\druki_content\Sync\Redirect\RedirectFileList
   */
  protected $payload;

  /**
   * RedirectQueueItem constructor.
   *
   * @param \Drupal\druki_content\Sync\Redirect\RedirectFileList $payload
   *   The redirect file list.
   */
  public function __construct(RedirectFileList $payload) {
    $this->payload = $payload;
  }

  /**
   * {@inheritdoc}
   */
  public function getPayload(): RedirectFileList {
    return $this->payload;
  }

}
