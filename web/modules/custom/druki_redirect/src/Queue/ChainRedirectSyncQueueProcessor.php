<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

use Drupal\druki_redirect\Repository\RedirectSyncQueueState;

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
   * The redirect sync queue state.
   */
  protected RedirectSyncQueueState $syncState;

  /**
   * Constructs a new RedirectSyncQueueProcessor object.
   *
   * @param \Drupal\druki_redirect\Repository\RedirectSyncQueueState $sync_state
   *   The redirect sync queue state.
   */
  public function __construct(RedirectSyncQueueState $sync_state) {
    $this->syncState = $sync_state;
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
        $this->syncState->storeEntityIds($ids);
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
