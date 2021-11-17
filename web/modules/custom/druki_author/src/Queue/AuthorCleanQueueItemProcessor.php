<?php

declare(strict_types=1);

namespace Drupal\druki_author\Queue;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_author\Data\AuthorCleanQueueItem;

/**
 * Provides author cleanup queue item processor.
 */
final class AuthorCleanQueueItemProcessor implements EntitySyncQueueItemProcessorInterface {

  /**
   * The author storage.
   */
  protected EntityStorageInterface $authorStorage;

  /**
   * The queue manager.
   */
  protected EntitySyncQueueManagerInterface $queueManager;

  /**
   * Constructs a new AuthorCleanQueueItemProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\druki\Queue\EntitySyncQueueManagerInterface $queue_manager
   *   The queue manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntitySyncQueueManagerInterface $queue_manager) {
    $this->authorStorage = $entity_type_manager->getStorage('druki_author');
    $this->queueManager = $queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(EntitySyncQueueItemInterface $item): bool {
    return $item instanceof AuthorCleanQueueItem;
  }

  /**
   * {@inheritdoc}
   */
  public function process(EntitySyncQueueItemInterface $item): array {
    $existing_ids = $this->authorStorage->getQuery()
      ->accessCheck(FALSE)
      ->execute();
    $synced_ids = $this->queueManager->getState()->getEntityIds();
    $removed_ids = \array_diff($existing_ids, $synced_ids);
    if (!$removed_ids) {
      return [];
    }
    $content_entities = $this->authorStorage->loadMultiple($removed_ids);
    $this->authorStorage->delete($content_entities);
    return [];
  }

}
