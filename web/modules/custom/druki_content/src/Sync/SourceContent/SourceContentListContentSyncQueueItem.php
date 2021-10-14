<?php

namespace Drupal\druki_content\Sync\SourceContent;

use Drupal\druki_content\Queue\ContentSyncQueueItemInterface;

/**
 * Provides queue item for synchronization content.
 */
final class SourceContentListContentSyncQueueItem implements ContentSyncQueueItemInterface {

  /**
   * The content to process.
   */
  protected SourceContentList $payload;

  /**
   * SourceContentListContentSyncQueueItem constructor.
   *
   * @param \Drupal\druki_content\Sync\SourceContent\SourceContentList $payload
   *   The queue payload.
   */
  public function __construct(SourceContentList $payload) {
    $this->payload = $payload;
  }

  /**
   * {@inheritdoc}
   */
  public function getPayload(): SourceContentList {
    return $this->payload;
  }

}
