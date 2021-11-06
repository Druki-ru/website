<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\druki_redirect\Queue\ChainRedirectSyncQueueProcessorInterface;
use Drupal\druki_redirect\Queue\RedirectSyncQueueItemInterface;
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
  protected ChainRedirectSyncQueueProcessorInterface $chainQueueProcessor;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->chainQueueProcessor = $container->get('druki_redirect.queue.chain_sync_processor');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function processItem(mixed $data): void {
    if (!$data instanceof RedirectSyncQueueItemInterface) {
      return;
    }
    $this->chainQueueProcessor->process($data);
  }

}
