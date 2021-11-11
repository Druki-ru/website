<?php

declare(strict_types=1);

namespace Drupal\druki\Queue;

/**
 * Provides default chained entity sync queue item processor.
 *
 * This processor looking for tagged services
 * 'druki_entity_sync_queue_item_processor' and loop over them in order to
 * process single queue item.
 */
final class ChainEntitySyncQueueItemProcessor implements ChainEntitySyncQueueItemProcessorInterface {

  /**
   * An array with queue item processors.
   *
   * @var \Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface[]
   */
  protected array $processors = [];

  /**
   * {@inheritdoc}
   */
  public function addProcessor(EntitySyncQueueItemProcessorInterface $processor): void {
    $this->processors[] = $processor;
  }

  /**
   * {@inheritdoc}
   */
  public function process(EntitySyncQueueItemInterface $item): array {
    $ids = [];
    foreach ($this->processors as $processor) {
      if (!$processor->isApplicable($item)) {
        continue;
      }
      $processed_ids = $processor->process($item);
      $ids = \array_unique(\array_merge($ids, $processed_ids));
    }
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(EntitySyncQueueItemInterface $item): bool {
    return TRUE;
  }

}
