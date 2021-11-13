<?php

declare(strict_types=1);

namespace Drupal\druki\Factory;

use Drupal\Core\Queue\QueueFactory;
use Drupal\druki\Queue\EntitySyncQueueManager;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;

/**
 * Provides factory for entity sync queue manager.
 */
final class EntitySyncQueueManagerFactory implements EntitySyncQueueManagerFactoryInterface {

  /**
   * An array with all currently instantiated queue managers.
   *
   * @var \Drupal\druki\Queue\EntitySyncQueueManagerInterface[]
   */
  protected array $queueManagers = [];

  /**
   * The queue factory.
   */
  protected QueueFactory $queueFactory;

  /**
   * The entity sync queue state factory.
   */
  protected EntitySyncQueueStateFactoryInterface $queueStateFactory;

  /**
   * Constructs a new EntitySyncQueueManagerFactory object.
   *
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   * @param \Drupal\druki\Factory\EntitySyncQueueStateFactoryInterface $queue_state_factory
   *   The queue state factory.
   */
  public function __construct(QueueFactory $queue_factory, EntitySyncQueueStateFactoryInterface $queue_state_factory) {
    $this->queueFactory = $queue_factory;
    $this->queueStateFactory = $queue_state_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function get(string $queue_name): EntitySyncQueueManagerInterface {
    // Because we process queues using derivatives, we should apply base queue
    // worker plugin ID to queue name.
    $queue_name = "druki_entity_sync:$queue_name";
    if (isset($this->queueManagers[$queue_name])) {
      return $this->queueManagers[$queue_name];
    }

    $queue = $this->queueFactory->get($queue_name);
    $queue_state_key = "$queue_name.state";
    $queue_state = $this->queueStateFactory->get($queue_state_key);
    $instance = new EntitySyncQueueManager($queue, $queue_state);
    $this->queueManagers[$queue_name] = $instance;
    return $instance;
  }

}
