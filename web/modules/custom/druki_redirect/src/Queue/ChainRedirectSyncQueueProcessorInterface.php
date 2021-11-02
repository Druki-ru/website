<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

/**
 * Provides interface for chained redirect sync queue item processor.
 */
interface ChainRedirectSyncQueueProcessorInterface extends RedirectSyncQueueItemProcessorInterface {

  /**
   * Adds processor into the list of available processors.
   *
   * @param \Drupal\druki_redirect\Queue\RedirectSyncQueueItemProcessorInterface $processor
   *   The processor instance.
   */
  public function addProcessor(RedirectSyncQueueItemProcessorInterface $processor): void;

}
