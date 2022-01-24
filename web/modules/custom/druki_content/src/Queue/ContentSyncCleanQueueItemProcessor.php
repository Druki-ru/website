<?php

namespace Drupal\druki_content\Queue;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_content\Data\ContentSyncCleanQueueItem;
use Drupal\druki_content\Repository\ContentStorage;
use Drupal\search\SearchIndexInterface;

/**
 * Provides synchronization clean queue processor.
 *
 * Purpose of this processor remove all content that exists on site but was not
 * found during synchronization — content deleted from source.
 *
 * @see \Drupal\druki\Repository\EntitySyncQueueStateInterface
 */
final class ContentSyncCleanQueueItemProcessor implements EntitySyncQueueItemProcessorInterface {

  /**
   * The queue manager.
   */
  protected EntitySyncQueueManagerInterface $queueManager;

  /**
   * A search index.
   */
  protected SearchIndexInterface $searchIndex;

  /**
   * A content entity storage.
   */
  protected ContentStorage $contentStorage;

  /**
   * ContentSyncCleanQueueItemProcessor constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\druki\Queue\EntitySyncQueueManagerInterface $queue_manager
   *   The queue manager.
   * @param \Drupal\search\SearchIndexInterface $search_index
   *   A search indexes.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntitySyncQueueManagerInterface $queue_manager, SearchIndexInterface $search_index) {
    $this->contentStorage = $entity_type_manager->getStorage('druki_content');
    $this->queueManager = $queue_manager;
    $this->searchIndex = $search_index;
  }

  /**
   * {@inheritdoc}
   */
  public function process(EntitySyncQueueItemInterface $item): array {
    $existing_ids = $this->contentStorage->getQuery()
      ->accessCheck(FALSE)
      ->execute();
    $synced_ids = $this->queueManager->getState()->getEntityIds();
    $removed_content_ids = \array_diff($existing_ids, $synced_ids);
    if ($removed_content_ids) {
      $this->cleanEntities($removed_content_ids);
    }

    return [];
  }

  /**
   * Deletes entities that not in the sync.
   *
   * @param array $content_ids
   *   An array with content entity IDs to delete.
   */
  protected function cleanEntities(array $content_ids): void {
    $content_entities = $this->contentStorage->loadMultiple($content_ids);
    $this->contentStorage->delete($content_entities);
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(EntitySyncQueueItemInterface $item): bool {
    return $item instanceof ContentSyncCleanQueueItem;
  }

}
