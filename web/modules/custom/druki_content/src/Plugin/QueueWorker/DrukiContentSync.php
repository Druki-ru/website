<?php

namespace Drupal\druki_content\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\druki_content\Queue\ContentSyncQueueItemInterface;
use Drupal\druki_content\Queue\ContentSyncQueueProcessor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides queue processor for content synchronization.
 *
 * The 'cron' option omitted, because we don't want to run the queue during
 * cron. We have separate command which allows us to run this queue in separate
 * PHP process and don't block other processes.
 *
 * @QueueWorker(
 *   id = "druki_content_sync",
 *   title = @Translation("Druki content sync."),
 * )
 */
final class DrukiContentSync extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The queue processor.
   */
  protected ContentSyncQueueProcessor $queueProcessor;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->queueProcessor = $container->get('druki_content.queue.content_sync_processor');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($queue_item): void {
    // First of all, check is item is value object we expected. We ignore all
    // values not passed via our object.
    if (!$queue_item instanceof ContentSyncQueueItemInterface) {
      return;
    }
    $this->queueProcessor->process($queue_item);
  }

}
