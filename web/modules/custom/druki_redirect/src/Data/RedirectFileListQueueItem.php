<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Data;

use Drupal\druki_redirect\Queue\RedirectSyncQueueItemInterface;

/**
 * Provides queue item with redirect file list.
 */
final class RedirectFileListQueueItem implements RedirectSyncQueueItemInterface {

  /**
   * The queue item payload.
   */
  protected RedirectFileList $payload;

  /**
   * Constructs a new RedirectFileListQueueItem object.
   *
   * @param \Drupal\druki_redirect\Data\RedirectFileList $redirect_file_list
   *   The redirect file list.
   */
  public function __construct(RedirectFileList $redirect_file_list) {
    $this->payload = $redirect_file_list;
  }

  /**
   * {@inheritdoc}
   */
  public function getPayload(): RedirectFileList {
    return $this->payload;
  }

}
