<?php

declare(strict_types=1);

namespace Drupal\druki\Factory;

use Drupal\Core\State\StateInterface;
use Drupal\druki\Repository\EntitySyncQueueState;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;

/**
 * Provides factory for building entity sync queue state repositories.
 */
final class EntitySyncQueueStateFactory {

  /**
   * An array with all currently instantiated repositories keyed by store key.
   *
   * @var \Drupal\druki\Repository\EntitySyncQueueStateInterface[]
   */
  protected array $repositories = [];

  /**
   * The key/value state storage.
   */
  protected StateInterface $state;

  /**
   * Constructs a new EntitySyncQueueStateFactory object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The key/value state storage.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * Gets instance of entity sync queue state repository.
   *
   * @param string $storage_key
   *   The storage key used by a repository.
   *
   * @return \Drupal\druki\Repository\EntitySyncQueueStateInterface
   *   The repository instance.
   */
  public function get(string $storage_key): EntitySyncQueueStateInterface {
    if (isset($this->repositories[$storage_key])) {
      return $this->repositories[$storage_key];
    }

    $instance = new EntitySyncQueueState($this->state, $storage_key);
    $this->repositories[$storage_key] = $instance;
    return $instance;
  }

}
