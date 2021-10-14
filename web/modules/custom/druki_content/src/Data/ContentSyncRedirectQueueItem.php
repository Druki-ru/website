<?php

namespace Drupal\druki_content\Data;

use Drupal\druki_content\Queue\ContentSyncQueueItemInterface;

/**
 * Provides queue item with redirects.
 */
final class ContentSyncRedirectQueueItem implements ContentSyncQueueItemInterface {

  /**
   * The redirect file list.
   */
  protected RedirectSourceFileList $payload;

  /**
   * ContentSyncRedirectQueueItem constructor.
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
