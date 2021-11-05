<?php

declare(strict_types=1);

namespace Drupal\druki_content\Repository;

use Drupal\Core\State\StateInterface;

/**
 * Provides content sync queue state.
 *
 * During content synchronization, queue processors going through each content
 * found in source directory. Creation and update process is simple enough, we
 * can use some unique values like 'slug' to find out, is there already content
 * for it or not and decide what to do: create or update entity.
 *
 * But we don't know exactly which content is already exists on the website but
 * was deleted from source. We only know the ones that remain in the source
 * repository.
 *
 * For this case, we need to store content IDs which was created or updated
 * during synchronization. At the end of synchronization, we simply fetch all
 * relevant content IDs from DB and compare with the stored ones. The diff
 * between that two will be content IDs that was presented on the website, but
 * was removed from source content.
 *
 * This storage is intended to store and clean that IDs.
 */
final class ContentSyncQueueState {

  /**
   * The storage key.
   */
  protected const STORAGE_KEY = 'druki_content.content_sync_queue_state';

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
