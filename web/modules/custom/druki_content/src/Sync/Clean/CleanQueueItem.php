<?php

namespace Drupal\druki_content\Sync\Clean;

use Drupal\druki_content\Sync\Queue\SyncQueueItemInterface;

/**
 * Provides queue item for clean up missing content.
 */
final class CleanQueueItem implements SyncQueueItemInterface {

  /**
   * The timestamp for the last update.
   *
   * @var int
   */
  protected $payload;

  /**
   * CleanQueueItem constructor.
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
