<?php

namespace Drupal\druki_content\Sync\SourceContent;

use Drupal\druki_content\Data\ContentSourceFileList;
use Drupal\druki_content\Queue\ContentSyncQueueItemInterface;

/**
 * Provides queue item for synchronization content.
 *
 * @todo Refactor to ContentSourceDocumentListQueueItem.
 */
final class SourceContentListContentSyncQueueItem implements ContentSyncQueueItemInterface {

  /**
   * The content to process.
   */
  protected ContentSourceFileList $payload;

  /**
   * SourceContentListContentSyncQueueItem constructor.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFileList $payload
   *   The queue payload.
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
