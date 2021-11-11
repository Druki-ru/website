<?php

declare(strict_types=1);

namespace Drupal\druki\Queue;

/**
 * Provides an interface for chained queue item processor.
 *
 * Chained queue item processor goes for each existing queue item processor.
 * First processor that ill be applicable take responsibility of processing
 * queue item.
 */
interface ChainEntitySyncQueueItemProcessorInterface extends EntitySyncQueueItemProcessorInterface {

  /**
   * Adds processor into the list of available processors.
   *
   * @param \Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface $processor
   *   The processor instance.
   */
  public function addProcessor(EntitySyncQueueItemProcessorInterface $processor): void;

}
