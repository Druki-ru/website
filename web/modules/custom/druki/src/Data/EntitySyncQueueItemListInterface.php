<?php

declare(strict_types=1);

namespace Drupal\druki\Data;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;

/**
 * Defines an interface for entity sync queue item collections.
 */
interface EntitySyncQueueItemListInterface extends \IteratorAggregate {

  /**
   * Adds queue item into the list.
   *
   * @param \Drupal\druki\Queue\EntitySyncQueueItemInterface $queue_item
   *   The queue item instance.
   *
   * @return $this
   */
  public function addQueueItem(EntitySyncQueueItemInterface $queue_item): self;

}
