<?php

declare(strict_types=1);

namespace Drupal\druki\Factory;

use Drupal\druki\Queue\EntitySyncQueueManagerInterface;

/**
 * Defines an interface for entity sync queue manager factories.
 */
interface EntitySyncQueueManagerFactoryInterface {

  /**
   * Gets instance of entity sync queue manager.
   *
   * @param string $queue_name
   *   The queue name.
   *
   * @return \Drupal\druki\Queue\EntitySyncQueueManagerInterface
   *   The queue manager instance.
   */
  public function get(string $queue_name): EntitySyncQueueManagerInterface;

}
