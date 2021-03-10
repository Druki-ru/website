<?php

namespace Drupal\druki_content\Sync\Clean;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_content\Sync\Queue\QueueItemInterface;
use Drupal\druki_content\Sync\Queue\QueueProcessorInterface;

/**
 * Provides synchronization clean queue processor.
 *
 * This processor will remove all content which has last sync timestamp lesser
 * than queue was built.
 */
final class CleanQueueProcessor implements QueueProcessorInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * CleanQueueProcessor constructor.
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
  public function process(QueueItemInterface $item): void {
    /** @var \Drupal\druki_content\Entity\Handler\Storage\DrukiContentStorage $druki_content_storage */
    $druki_content_storage = $this->entityTypeManager->getStorage('druki_content');
    $druki_content_storage->cleanOutdated($item->getPayload());
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(QueueItemInterface $item): bool {
    return $item instanceof CleanQueueItem;
  }

}
