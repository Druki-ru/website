<?php

namespace Drupal\druki_content\Sync\Queue;

/**
 * Provides processing for sync queue items.
 */
final class QueueProcessor implements QueueProcessorInterface {

  /**
   * The list of available queue processors.
   *
   * @var \Drupal\druki_content\Sync\Queue\QueueProcessorInterface[]
   */
  protected array $processors = [];

  /**
   * Adds loader to the list.
   *
   * @param \Drupal\druki_content\Sync\Queue\QueueProcessorInterface $processor
   *   The loader instance.
   */
  public function addProcessor(QueueProcessorInterface $processor): void {
    $this->processors[] = $processor;
  }

  /**
   * {@inheritdoc}
   */
  public function process(QueueItemInterface $item): void {
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
  public function isApplicable(QueueItemInterface $item): bool {
    return FALSE;
  }

}
