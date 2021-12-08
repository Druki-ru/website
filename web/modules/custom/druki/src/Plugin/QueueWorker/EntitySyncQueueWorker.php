<?php

declare(strict_types=1);

namespace Drupal\druki\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\druki\Queue\ChainEntitySyncQueueItemProcessorInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides common queue worker for entity sync.
 *
 * @QueueWorker(
 *   id = "druki_entity_sync",
 *   title = @Translation("Entity sync queue worker"),
 *   deriver = "Drupal\druki\Plugin\Deriver\EntitySyncQueueWorkerDeriver",
 * )
 */
final class EntitySyncQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The chain queue processor.
   */
  protected ChainEntitySyncQueueItemProcessorInterface $chainQueueItemProcessor;

  /**
   * The entity sync queue manager.
   */
  protected EntitySyncQueueManagerInterface $queueManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->chainQueueItemProcessor = $container->get('druki.queue.chain_entity_sync_processor');
    $instance->queueManager = $container->get('druki.factory.entity_sync_queue_manager')->get($plugin_definition['id']);
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function processItem(mixed $data): void {
    if (!$data instanceof EntitySyncQueueItemInterface) {
      return;
    }
    $ids = $this->chainQueueItemProcessor->process($data);
    $this->queueManager->getState()->storeEntityIds($ids);
  }

}
