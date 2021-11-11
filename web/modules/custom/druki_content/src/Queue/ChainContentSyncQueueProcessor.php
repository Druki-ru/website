<?php

namespace Drupal\druki_content\Queue;

/**
 * Provides chained processor for content sync queue.
 */
final class ChainContentSyncQueueProcessor implements ContentSyncQueueProcessorInterface {

  /**
   * The list of available queue processors.
   *
   * @var \Drupal\druki_content\Queue\ContentSyncQueueProcessorInterface[]
   */
  protected array $processors = [];

  /**
   * The sync queue manager.
   */
  protected ContentSyncQueueManagerInterface $queueManager;

  /**
   * Constructs a new ChainContentSyncQueueProcessor object.
   *
   * @param \Drupal\druki_content\Queue\ContentSyncQueueManagerInterface $queue_manager
   *   The queue manager.
   */
  public function __construct(ContentSyncQueueManagerInterface $queue_manager) {
    $this->queueManager = $queue_manager;
  }

  /**
   * Adds loader to the list.
   *
   * @param \Drupal\druki_content\Queue\ContentSyncQueueProcessorInterface $processor
   *   The loader instance.
   */
  public function addProcessor(ContentSyncQueueProcessorInterface $processor): void {
    $this->processors[] = $processor;
  }

  /**
   * {@inheritdoc}
   */
  public function process(ContentSyncQueueItemInterface $item): array {
    foreach ($this->processors as $processor) {
      if ($processor->isApplicable($item)) {
        $ids = $processor->process($item);
        if (!empty($ids)) {
          $this->queueManager->getState()->storeEntityIds($ids);
        }
        break;
      }
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(ContentSyncQueueItemInterface $item): bool {
    return TRUE;
  }

}
