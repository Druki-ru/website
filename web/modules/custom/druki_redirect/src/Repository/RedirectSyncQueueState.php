<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Repository;

use Drupal\Core\State\StateInterface;

/**
 * Provides content sync queue state.
 *
 * @see \Drupal\druki_content\Repository\ContentSyncQueueState
 */
final class RedirectSyncQueueState {

  /**
   * The storage key.
   */
  protected const STORAGE_KEY = 'druki_redirect.redirect_sync_queue_state';

  /**
   * The state storage.
   */
  protected StateInterface $state;

  /**
   * Constructs a new ContentSyncQueueState object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * Store processed list of IDs.
   *
   * @param array $ids
   *   The entity IDs.
   */
  public function storeEntityIds(array $ids): void {
    $current_ids = $this->getStoredEntityIds();
    $combined_ids = \array_unique(\array_merge($current_ids, $ids));
    $this->state->set(self::STORAGE_KEY, \array_values($combined_ids));
  }

  /**
   * Gets stored content IDs.
   *
   * @return array
   *   An array with entity IDs.
   */
  public function getStoredEntityIds(): array {
    return $this->state->get(self::STORAGE_KEY, []);
  }

  /**
   * Clear state storage from any values.
   */
  public function delete(): void {
    $this->state->delete(self::STORAGE_KEY);
  }

}
