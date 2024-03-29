<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_redirect\Data\RedirectCleanQueueItem;

/**
 * Provides redirect clean queue item processor.
 *
 * @see \Drupal\druki\Repository\EntitySyncQueueStateInterface
 */
final class RedirectCleanQueueItemProcessor implements EntitySyncQueueItemProcessorInterface {

  /**
   * The redirect storage.
   */
  protected EntityStorageInterface $redirectStorage;

  /**
   * The redirect sync queue manager.
   */
  protected EntitySyncQueueManagerInterface $queueManager;

  /**
   * Constructs a new RedirectCleanQueueItemProcessor object.
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
    $this->redirectStorage = $entity_type_manager->getStorage('redirect');
    $this->queueManager = $queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(EntitySyncQueueItemInterface $item): bool {
    return $item instanceof RedirectCleanQueueItem;
  }

  /**
   * {@inheritdoc}
   */
  public function process(EntitySyncQueueItemInterface $item): array {
    $existing_ids = $this->redirectStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('druki_redirect', TRUE)
      ->execute();
    $synced_ids = $this->queueManager->getState()->getEntityIds();
    $removed_redirect_ids = \array_diff($existing_ids, $synced_ids);
    if (!$removed_redirect_ids) {
      return [];
    }
    $content_entities = $this->redirectStorage->loadMultiple($removed_redirect_ids);
    $this->redirectStorage->delete($content_entities);
    return [];
  }

}
