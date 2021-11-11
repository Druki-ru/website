<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\druki\Queue\ChainEntitySyncQueueItemProcessorInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki_redirect\Queue\RedirectSyncQueueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides queue worker for 'druki_redirect_sync' queue.
 *
 * @QueueWorker(
 *   id = "druki_redirect_sync",
 *   title = @Translation("Druki Redirect sync queue"),
 * )
 */
final class DrukiRedirectSyncQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The chain queue processor.
   */
  protected ChainEntitySyncQueueItemProcessorInterface $chainQueueProcessor;

  /**
   * The redirect sync queue manager.
   */
  protected RedirectSyncQueueManager $queueManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->chainQueueProcessor = $container->get('druki.queue.chain_entity_sync_processor');
    $instance->queueManager = $container->get('druki_redirect.queue.sync_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function processItem(mixed $data): void {
    if (!$data instanceof EntitySyncQueueItemInterface) {
      return;
    }
    $ids = $this->chainQueueProcessor->process($data);
    $this->queueManager->getState()->storeEntityIds($ids);
  }

}
