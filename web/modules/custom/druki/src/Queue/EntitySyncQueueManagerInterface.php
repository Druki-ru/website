<?php

declare(strict_types=1);

namespace Drupal\druki\Queue;

use Drupal\Core\Queue\QueueInterface;
use Drupal\druki\Data\EntitySyncQueueItemListInterface;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;

/**
 * Defines an interface for entity sync queue manager.
 */
interface EntitySyncQueueManagerInterface {

  /**
   * Fills queue with provided items.
   *
   * This operation clears all previously added queue, reset state and builds
   * a new queue and state.
   *
   * @param \Drupal\druki\Data\EntitySyncQueueItemListInterface $queue_items
   *   The queue items to fill queue with.
   *
   * @return $this
   */
  public function fillQueue(EntitySyncQueueItemListInterface $queue_items): self;

  /**
   * Gets queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   The queue instance.
   */
  public function getQueue(): QueueInterface;

  /**
   * Gets queue state.
   *
   * @return \Drupal\druki\Repository\EntitySyncQueueStateInterface
   *   The queue state.
   */
  public function getState(): EntitySyncQueueStateInterface;

  /**
   * Deletes everything related to queue.
   */
  public function delete(): void;

}
