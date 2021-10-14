<?php

namespace Drupal\druki_content\Data;

use Drupal\druki_content\Queue\ContentSyncQueueItemInterface;

/**
 * Provides queue item for clean up missing content.
 */
final class ContentSyncCleanQueueItem implements ContentSyncQueueItemInterface {

  /**
   * The timestamp for the last update.
   */
  protected int $payload;

  /**
   * ContentSyncCleanQueueItem constructor.
   *
   * @param int $timestamp
   *   The last update timestamp.
   */
  public function __construct(int $timestamp) {
    $this->payload = $timestamp;
  }

  /**
   * {@inheritdoc}
   */
  public function getPayload(): int {
    return $this->payload;
  }

}
