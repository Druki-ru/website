<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

/**
 * Provides processor for redirect sync queue items.
 */
final class ChainRedirectSyncQueueProcessor implements ChainRedirectSyncQueueProcessorInterface {

  /**
   * The queue item processors.
   *
   * @var \Drupal\druki_redirect\Queue\RedirectSyncQueueItemProcessorInterface[]
   */
  protected array $processors = [];

  /**
   * The queue manager.
   */
  protected RedirectSyncQueueManagerInterface $queueManager;

  /**
   * Constructs a new RedirectSyncQueueProcessor object.
   *
   * @param \Drupal\druki_redirect\Queue\RedirectSyncQueueManagerInterface $queue_manager
   *   The queue manager.
   */
  public function __construct(RedirectSyncQueueManagerInterface $queue_manager) {
    $this->queueManager = $queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function process(RedirectSyncQueueItemInterface $item): array {
    foreach ($this->processors as $processor) {
      if (!$processor->isApplicable($item)) {
        continue;
      }
      $ids = $processor->process($item);
      if (!empty($ids)) {
        $this->queueManager->getState()->storeEntityIds($ids);
      }
      break;
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(RedirectSyncQueueItemInterface $item): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function addProcessor(RedirectSyncQueueItemProcessorInterface $processor): void {
    $this->processors[] = $processor;
  }

}
