<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;

/**
 * Provides queue item for content source file list.
 */
final class ContentSourceFileListQueueItem implements EntitySyncQueueItemInterface {

  /**
   * The content source file list.
   */
  protected ContentSourceFileList $payload;

  /**
   * Constructs a new ContentSourceFileListQueueItem object.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFileList $payload
   *   The content source file list.
   */
  public function __construct(ContentSourceFileList $payload) {
    $this->payload = $payload;
  }

  /**
   * {@inheritdoc}
   */
  public function getPayload(): ContentSourceFileList {
    return $this->payload;
  }

}
