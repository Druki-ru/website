<?php

namespace Drupal\druki_content\Queue;

/**
 * Provides processing for sync queue items.
 */
final class ContentSyncQueueProcessor implements ContentSyncQueueProcessorInterface {

  /**
   * The list of available queue processors.
   *
   * @var \Drupal\druki_content\Queue\ContentSyncQueueProcessorInterface
   */
  protected array $processors = [];

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
  public function process(ContentSyncQueueItemInterface $item): void {
    foreach ($this->processors as $processor) {
      if ($processor->isApplicable($item)) {
        $processor->process($item);
        break;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(ContentSyncQueueItemInterface $item): bool {
    return FALSE;
  }

}
