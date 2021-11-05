<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Site\Settings;
use Drupal\druki_redirect\Data\RedirectCleanQueueItem;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\druki_redirect\Finder\RedirectFileFinder;
use Drupal\druki_redirect\Repository\RedirectSyncQueueState;

/**
 * Provides redirect sync queue manager.
 */
final class RedirectSyncQueueManager implements RedirectSyncQueueManagerInterface {

  /**
   * The queue name used for redirect files.
   */
  public const QUEUE_NAME = 'druki_redirect_sync';

  /**
   * The redirect file finder.
   */
  protected RedirectFileFinder $redirectFileFinder;

  /**
   * The sync queue.
   */
  protected QueueInterface $queue;

  /**
   * The redirect sync queue state.
   */
  protected RedirectSyncQueueState $syncState;

  /**
   * Constructs a new RedirectSyncQueueManager object.
   *
   * @param \Drupal\druki_redirect\Finder\RedirectFileFinder $redirect_file_finder
   *   The redirect file finder.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   * @param \Drupal\druki_redirect\Repository\RedirectSyncQueueState $sync_state
   *   The redirect sync queue state.
   */
  public function __construct(RedirectFileFinder $redirect_file_finder, QueueFactory $queue_factory, RedirectSyncQueueState $sync_state) {
    $this->redirectFileFinder = $redirect_file_finder;
    $this->queue = $queue_factory->get(self::QUEUE_NAME);
    $this->syncState = $sync_state;
  }

  /**
   * {@inheritdoc}
   */
  public function buildFromDirectories(array $directories): void {
    $this->delete();
    $redirect_file_list = $this->redirectFileFinder->findAll($directories);
    if ($redirect_file_list->getIterator()->count()) {
      $this->addRedirectFileList($redirect_file_list);
    }
    $this->addCleanOperation();
  }

  /**
   * Adds clean operation.
   */
  protected function addCleanOperation(): void {
    $this->getQueue()->createItem(new RedirectCleanQueueItem());
  }

  /**
   * {@inheritdoc}
   */
  public function delete(): void {
    $this->getQueue()->deleteQueue();
    $this->getState()->delete();
  }

  /**
   * Adds redirect file list items into queue.
   *
   * @param \Drupal\druki_redirect\Data\RedirectFileList $redirect_file_list
   *   The redirect file list object.
   */
  protected function addRedirectFileList(RedirectFileList $redirect_file_list): void {
    $items_per_queue = Settings::get('entity_update_batch_size', 50);
    $redirect_files_array = $redirect_file_list->getIterator()->getArrayCopy();
    $chunks = \array_chunk($redirect_files_array, $items_per_queue);
    foreach ($chunks as $chunk) {
      $content_source_file_list = new RedirectFileList();
      /** @var \Drupal\druki_redirect\Data\RedirectFile $redirect_file */
      foreach ($chunk as $redirect_file) {
        $content_source_file_list->addFile($redirect_file);
      }
      $queue_item = new RedirectFileListQueueItem($content_source_file_list);
      $this->getQueue()->createItem($queue_item);
    }
  }

  /**
   * Gets queue state.
   *
   * @return \Drupal\druki_redirect\Repository\RedirectSyncQueueState
   *   The queue state storage.
   */
  public function getState(): RedirectSyncQueueState {
    return $this->syncState;
  }

  /**
   * Gets queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   The queue.
   */
  public function getQueue(): QueueInterface {
    return $this->queue;
  }

}
