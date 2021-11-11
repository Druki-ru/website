<?php

declare(strict_types=1);

namespace Drupal\druki\Repository;

use Drupal\Core\State\StateInterface;

/**
 * Provides default implementation for entity sync queue state repositories.
 */
final class EntitySyncQueueState implements EntitySyncQueueStateInterface {

  /**
   * The key/value state storage.
   */
  protected StateInterface $state;

  /**
   * The storage key for a current repository.
   */
  protected string $storageKey;

  /**
   * Constructs a new EntitySyncQueueState object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The key/value state storage.
   * @param string $storage_key
   *   The storage key to use with a current repository.
   */
  public function __construct(StateInterface $state, string $storage_key) {
    $this->state = $state;
    $this->storageKey = $storage_key;
  }

  /**
   * {@inheritdoc}
   */
  public function storeEntityIds(array $ids): void {
    $current_ids = $this->getEntityIds();
    $combined_ids = \array_unique(\array_merge($current_ids, $ids));
    $this->state->set($this->storageKey, \array_values($combined_ids));
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityIds(): array {
    return $this->state->get($this->storageKey, []);
  }

  /**
   * {@inheritdoc}
   */
  public function delete(): void {
    $this->state->delete($this->storageKey);
  }

}
