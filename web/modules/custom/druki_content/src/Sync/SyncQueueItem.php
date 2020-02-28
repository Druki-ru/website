<?php

namespace Drupal\druki_content\Sync;

use Drupal\druki_content\SourceContent\SourceContentList;

/**
 * Provides value object that holds queue data for single processing.
 */
final class SyncQueueItem {

  /**
   * The queue operation for source content synchronization.
   *
   * This operation will create or update content from source files added to
   * item.
   */
  public const SYNCHRONIZATION = 'synchronization';

  /**
   * The queue clean operation.
   *
   * This operation will clean content that still remains on site, but was
   * not presented during sync process.
   */
  public const CLEAN = 'clean';

  /**
   * The queue item operation.
   *
   * @var string
   */
  protected $operation;

  /**
   * The operation payload.
   *
   * @var \Drupal\druki_content\SourceContent\SourceContentList|int
   */
  protected $payload;

  /**
   * Constructs a new SyncQueueItem object.
   *
   * @param string $operation
   *   The operation type. See constants for available values.
   * @param \Drupal\druki_content\SourceContent\SourceContentList|int $payload
   *   THe operation data.
   */
  public function __construct(string $operation, $payload) {
    $this->setOperation($operation);
    $this->setPayload($payload);
  }

  /**
   * Sets operation for current queue item.
   *
   * @param string $operation
   *   The operation name.
   */
  protected function setOperation(string $operation): void {
    $allowed = [self::SYNCHRONIZATION, self::CLEAN];
    if (!in_array($operation, $allowed)) {
      throw new InvalidSyncQueueOperationException($operation, $allowed);
    }

    $this->operation = $operation;
  }

  /**
   * Sets the value for this queue item.
   *
   * @param \Drupal\druki_content\SourceContent\SourceContentList|int $payload
   *   The value of queue item to process.
   */
  protected function setPayload($payload): void {
    switch ($this->operation) {
      case self::SYNCHRONIZATION:
        if (!$payload instanceof SourceContentList) {
          throw new \InvalidArgumentException('The synchronization queue operation only allowed for \Drupal\druki_content\SourceContent\SourceContentList as data.');
        }
        break;

      case self::CLEAN:
        if (!is_int($payload)) {
          throw new \InvalidArgumentException('The clean queue operation only allowed for integer.');
        }
        break;
    }

    $this->payload = $payload;
  }

}
