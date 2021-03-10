<?php

namespace Drupal\druki_content\Sync\SourceContent;

use Drupal\druki_content\Sync\Queue\SyncQueueItemInterface;

/**
 * Provides queue item for synchronization content.
 */
final class SourceContentListQueueItem implements SyncQueueItemInterface {

  /**
   * The content to process.
   *
   * @var \Drupal\druki_content\Sync\SourceContent\SourceContentList
   */
  protected $payload;

  /**
   * SourceContentListQueueItem constructor.
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
