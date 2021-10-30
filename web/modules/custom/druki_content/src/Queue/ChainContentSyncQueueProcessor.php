<?php

namespace Drupal\druki_content\Queue;

use Drupal\druki_content\Repository\ContentSyncQueueState;

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
   * The content sync queue state storage.
   */
  protected ContentSyncQueueState $syncState;

  /**
   * Constructs a new ChainContentSyncQueueProcessor object.
   *
   * @param \Drupal\druki_content\Repository\ContentSyncQueueState $sync_state
   *   The content sync queue state storage.
   */
  public function __construct(ContentSyncQueueState $sync_state) {
    $this->syncState = $sync_state;
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
          $this->syncState->storeEntityIds($ids);
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
