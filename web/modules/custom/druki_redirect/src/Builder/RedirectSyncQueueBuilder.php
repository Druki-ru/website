<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Builder;

use Drupal\druki\Data\EntitySyncQueueItemList;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_redirect\Data\RedirectCleanQueueItem;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\druki_redirect\Finder\RedirectFileFinder;

/**
 * Builds redirect sync queue items.
 */
final class RedirectSyncQueueBuilder implements RedirectSyncQueueBuilderInterface {

  /**
   * The redirect file finder.
   */
  protected RedirectFileFinder $redirectFileFinder;

  /**
   * The redirect sync queue manager.
   */
  protected EntitySyncQueueManagerInterface $queueManager;

  /**
   * Constructs a new RedirectSyncQueueBuilder object.
   *
   * @param \Drupal\druki_redirect\Finder\RedirectFileFinder $redirect_file_finder
   *   The redirect file finder.
   * @param \Drupal\druki\Queue\EntitySyncQueueManagerInterface $queue_manager
   *   The queue manager.
   */
  public function __construct(RedirectFileFinder $redirect_file_finder, EntitySyncQueueManagerInterface $queue_manager) {
    $this->redirectFileFinder = $redirect_file_finder;
    $this->queueManager = $queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildFromDirectories(array $directories): void {
    $redirect_file_list = $this->redirectFileFinder->findAll($directories);

    $queue_items = new EntitySyncQueueItemList();
    $queue_items->addQueueItem(new RedirectFileListQueueItem($redirect_file_list));
    $queue_items->addQueueItem(new RedirectCleanQueueItem());

    $this->queueManager->fillQueue($queue_items);
  }

}
