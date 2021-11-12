<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\Core\Site\Settings;
use Drupal\druki\Data\EntitySyncQueueItemList;
use Drupal\druki\Data\EntitySyncQueueItemListInterface;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_content\Data\ContentSourceFileList;
use Drupal\druki_content\Data\ContentSourceFileListQueueItem;
use Drupal\druki_content\Data\ContentSyncCleanQueueItem;
use Drupal\druki_content\Finder\ContentSourceFileFinder;

/**
 * Provides content sync queue builder.
 */
final class ContentSyncQueueBuilder implements ContentSyncQueueBuilderInterface {

  /**
   * The entity sync queue manager.
   */
  protected EntitySyncQueueManagerInterface $queueManager;

  /**
   * The content source file finder.
   */
  protected ContentSourceFileFinder $sourceFileFinder;

  /**
   * Constructs a new ContentSyncQueueBuilder object.
   *
   * @param \Drupal\druki\Queue\EntitySyncQueueManagerInterface $queue_manager
   *   The queue manager.
   * @param \Drupal\druki_content\Finder\ContentSourceFileFinder $source_file_finder
   *   The source file finder.
   */
  public function __construct(EntitySyncQueueManagerInterface $queue_manager, ContentSourceFileFinder $source_file_finder) {
    $this->queueManager = $queue_manager;
    $this->sourceFileFinder = $source_file_finder;
  }

  /**
   * {@inheritdoc}
   */
  public function buildFromPath(string $directory): void {
    $queue_items = new EntitySyncQueueItemList();

    $content_source_file_list = $this->sourceFileFinder->findAll($directory);
    if ($content_source_file_list->getIterator()->count()) {
      $this->addContentSourceFileList($content_source_file_list, $queue_items);
    }
    $queue_items->addQueueItem(new ContentSyncCleanQueueItem());

    $this->queueManager->fillQueue($queue_items);
  }

  /**
   * Splits content into smaller chunks and adds them into queue.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFileList $content_source_file_list
   *   The content source file list.
   * @param \Drupal\druki\Data\EntitySyncQueueItemListInterface $queue_items
   *   The queue items.
   */
  protected function addContentSourceFileList(ContentSourceFileList $content_source_file_list, EntitySyncQueueItemListInterface $queue_items): void {
    $items_per_queue = Settings::get('entity_update_batch_size', 50);
    $source_files_array = $content_source_file_list->getIterator()->getArrayCopy();
    $chunks = \array_chunk($source_files_array, $items_per_queue);
    foreach ($chunks as $chunk) {
      $content_source_file_list = new ContentSourceFileList();
      /** @var \Drupal\druki_content\Data\ContentSourceFile $content_source_file */
      foreach ($chunk as $content_source_file) {
        $content_source_file_list->addFile($content_source_file);
      }
      $queue_item = new ContentSourceFileListQueueItem($content_source_file_list);
      $queue_items->addQueueItem($queue_item);
    }
  }

}
