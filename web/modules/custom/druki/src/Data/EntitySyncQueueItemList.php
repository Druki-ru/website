<?php

declare(strict_types=1);

namespace Drupal\druki\Data;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;

/**
 * Provides a list with queue items to fill entity sync queue.
 */
final class EntitySyncQueueItemList implements EntitySyncQueueItemListInterface {

  /**
   * An array with queue items.
   *
   * @var \Drupal\druki\Queue\EntitySyncQueueItemInterface[]
   */
  protected array $items = [];

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  /**
   * {@inheritdoc}
   */
  public function addQueueItem(EntitySyncQueueItemInterface $queue_item): EntitySyncQueueItemListInterface {
    $this->items[] = $queue_item;
    return $this;
  }

}
