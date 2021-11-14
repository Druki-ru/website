<?php

declare(strict_types=1);

namespace Drupal\druki\Queue;

use Drupal\Core\Queue\QueueInterface;
use Drupal\druki\Data\EntitySyncQueueItemListInterface;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;

/**
 * Provides default implementation for entity sync queue manager.
 */
final class EntitySyncQueueManager implements EntitySyncQueueManagerInterface {

  /**
   * The queue storage.
   */
  protected QueueInterface $queue;

  /**
   * The queue sync state.
   */
  protected EntitySyncQueueStateInterface $queueState;

  /**
   * Constructs a new EntitySyncQueueManager object.
   *
   * @param \Drupal\Core\Queue\QueueInterface $queue
   *   The queue storage.
   * @param \Drupal\druki\Repository\EntitySyncQueueStateInterface $queue_state
   *   The queue sync state.
   */
  public function __construct(QueueInterface $queue, EntitySyncQueueStateInterface $queue_state) {
    $this->queue = $queue;
    $this->queueState = $queue_state;
  }

  /**
   * {@inheritdoc}
   */
  public function fillQueue(EntitySyncQueueItemListInterface $queue_items): void {
    $this->delete();
    foreach ($queue_items as $queue_item) {
      $this->getQueue()->createItem($queue_item);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getState(): EntitySyncQueueStateInterface {
    return $this->queueState;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueue(): QueueInterface {
    return $this->queue;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(): void {
    $this->getState()->delete();
    $this->getQueue()->deleteQueue();
  }

}
