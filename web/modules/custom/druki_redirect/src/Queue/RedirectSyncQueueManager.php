<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Site\Settings;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Finder\RedirectFileFinder;

/**
 * Provides redirect sync queue manager.
 */
final class RedirectSyncQueueManager {

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
   * Constructs a new RedirectSyncQueueManager object.
   *
   * @param \Drupal\druki_redirect\Finder\RedirectFileFinder $redirect_file_finder
   *   The redirect file finder.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   */
  public function __construct(RedirectFileFinder $redirect_file_finder, QueueFactory $queue_factory) {
    $this->redirectFileFinder = $redirect_file_finder;
    $this->queue = $queue_factory->get(self::QUEUE_NAME);
  }

  /**
   * Builds queue from provided directories.
   *
   * @param array $directories
   *   An array with directories where to look for 'redirects.csv' file.
   *
   * @see \Drupal\druki_redirect\Finder\RedirectFileFinder
   */
  public function buildFromDirectories(array $directories): void {
    $this->delete();
    $redirect_file_list = $this->redirectFileFinder->findAll($directories);
    $items_per_queue = Settings::get('entity_update_batch_size', 50);
    $redirect_files_array = $redirect_file_list->getIterator()->getArrayCopy();
    $chunks = \array_chunk($redirect_files_array, $items_per_queue);
    foreach ($chunks as $chunk) {
      $content_source_file_list = new RedirectFileList();
      /** @var \Drupal\druki_redirect\Data\RedirectFile $redirect_file */
      foreach ($chunk as $redirect_file) {
        $content_source_file_list->addFile($redirect_file);
      }
      $this->queue->createItem($content_source_file_list);
    }
  }

  /**
   * Deletes everything related to the queue.
   */
  public function delete(): void {
    $this->queue->deleteQueue();
  }

}
