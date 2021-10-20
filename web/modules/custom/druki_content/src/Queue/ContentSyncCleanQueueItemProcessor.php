<?php

namespace Drupal\druki_content\Queue;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_content\Data\ContentSyncCleanQueueItem;

/**
 * Provides synchronization clean queue processor.
 *
 * This processor will remove all content which has last sync timestamp lesser
 * than queue was built.
 */
final class ContentSyncCleanQueueItemProcessor implements ContentSyncQueueProcessorInterface {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * ContentSyncCleanQueueItemProcessor constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function process(ContentSyncQueueItemInterface $item): void {
    /** @var \Drupal\druki_content\Repository\DrukiContentStorage $druki_content_storage */
    $druki_content_storage = $this->entityTypeManager->getStorage('druki_content');
    $druki_content_storage->cleanOutdated($item->getPayload());
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(ContentSyncQueueItemInterface $item): bool {
    return $item instanceof ContentSyncCleanQueueItem;
  }

}
