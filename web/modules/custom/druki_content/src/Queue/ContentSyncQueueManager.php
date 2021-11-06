<?php

namespace Drupal\druki_content\Queue;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\RequeueException;
use Drupal\Core\Queue\SuspendQueueException;
use Drupal\Core\Site\Settings;
use Drupal\druki_content\Data\ContentSourceFileList;
use Drupal\druki_content\Data\ContentSourceFileListQueueItem;
use Drupal\druki_content\Data\ContentSyncCleanQueueItem;
use Drupal\druki_content\Finder\ContentSourceFileFinder;
use Drupal\druki_content\Repository\ContentSyncQueueState;

/**
 * Provides queue manager for synchronization content.
 */
final class ContentSyncQueueManager implements ContentSyncQueueManagerInterface {

  /**
   * The content source file finder.
   */
  protected ContentSourceFileFinder $contentSourceFileFinder;

  /**
   * The queue with synchronization items.
   */
  protected QueueInterface $queue;

  /**
   * The queue worker.
   */
  protected QueueWorkerManagerInterface $queueWorker;

  /**
   * The queue state.
   */
  protected ContentSyncQueueState $queueState;

  /**
   * Constructs a new SynchronizationQueueBuilder object.
   *
   * @param \Drupal\druki_content\Finder\ContentSourceFileFinder $content_source_file_finder
   *   The source content finder.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   * @param \Drupal\Core\Queue\QueueWorkerManagerInterface $queue_worker
   *   The queue manager.
   * @param \Drupal\druki_content\Repository\ContentSyncQueueState $sync_state
   *   The content sync state.
   */
  public function __construct(ContentSourceFileFinder $content_source_file_finder, QueueFactory $queue_factory, QueueWorkerManagerInterface $queue_worker, ContentSyncQueueState $sync_state) {
    $this->contentSourceFileFinder = $content_source_file_finder;
    $this->queue = $queue_factory->get(self::QUEUE_NAME);
    $this->queueWorker = $queue_worker;
    $this->queueState = $sync_state;
  }

  /**
   * {@inheritdoc}
   */
  public function buildFromPath(string $directory): void {
    $this->delete();
    $content_source_file_list = $this->contentSourceFileFinder->findAll($directory);
    if ($content_source_file_list->getIterator()->count()) {
      $this->addContentSourceFileList($content_source_file_list);
    }
    $this->addCleanOperation();
  }

  /**
   * {@inheritdoc}
   */
  public function delete(): void {
    $this->queue->deleteQueue();
    $this->queueState->delete();
  }

  /**
   * Builds new queue from source content list.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFileList $content_source_file_list
   *   The content source file list.
   */
  protected function addContentSourceFileList(ContentSourceFileList $content_source_file_list): void {
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
      $this->queue->createItem($queue_item);
    }
  }

  /**
   * Adds clean operation into queue.
   */
  protected function addCleanOperation(): void {
    $this->queue->createItem(new ContentSyncCleanQueueItem());
  }

  /**
   * {@inheritdoc}
   */
  public function getState(): ContentSyncQueueState {
    return $this->queueState;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueue(): QueueInterface {
    return $this->queue;
  }

  /**
   * {@inheritdoc}
   */
  public function run(int $time_limit = 15): int {
    /** @var \Drupal\Core\Queue\QueueWorkerInterface $queue_worker */
    $queue_worker = $this->queueWorker->createInstance('druki_content_sync');

    // Make sure queue exists. There is no harm in trying to recreate an
    // existing queue.
    $this->queue->createQueue();
    $end = \time() + $time_limit;
    $lease_time = $time_limit;
    $count = 0;
    while (\time() < $end && ($item = $this->queue->claimItem($lease_time))) {
      try {
        $queue_worker->processItem($item->data);
        $this->queue->deleteItem($item);
        $count++;
      }
      catch (RequeueException $e) {
        // The worker requested the task be immediately requeued.
        $this->queue->releaseItem($item);
      }
      catch (SuspendQueueException $e) {
        // If the worker indicates there is a problem with the whole queue,
        // release the item and skip to the next queue.
        $this->queue->releaseItem($item);
      }
      catch (\Exception $e) {
        // In case of any other kind of exception, log this information and
        // delete that item from queue so it wont block processing other items.
        // Maybe this should somehow notify about problem.
        $this->queue->deleteItem($item);
        $count++;
        \watchdog_exception('druki_content', $e);
      }
    }

    return $count;
  }

}
