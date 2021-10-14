<?php

namespace Drupal\druki_content\Queue;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\RequeueException;
use Drupal\Core\Queue\SuspendQueueException;
use Drupal\Core\Site\Settings;
use Drupal\Core\State\StateInterface;
use Drupal\druki_content\Data\ContentSyncCleanQueueItem;
use Drupal\druki_content\Data\ContentSyncRedirectQueueItem;
use Drupal\druki_content\Finder\RedirectSourceFileFinder;
use Drupal\druki_content\Sync\SourceContent\SourceContentFinder;
use Drupal\druki_content\Sync\SourceContent\SourceContentListContentSyncQueueItem;

/**
 * Provides queue manager for synchronization content.
 */
final class ContentSyncQueueManager {

  /**
   * The queue name used for synchronisation.
   */
  public const QUEUE_NAME = 'druki_content_sync';

  /**
   * The source content finder.
   */
  protected SourceContentFinder $contentFinder;

  /**
   * The queue with synchronization items.
   */
  protected QueueInterface $queue;

  /**
   * The state storage.
   */
  protected StateInterface $state;

  /**
   * The system time.
   */
  protected TimeInterface $time;

  /**
   * The queue worker.
   */
  protected QueueWorkerManagerInterface $queueWorker;

  /**
   * The redirect finder.
   */
  protected RedirectSourceFileFinder $redirectFinder;

  /**
   * Constructs a new SynchronizationQueueBuilder object.
   *
   * @param \Drupal\druki_content\Sync\SourceContent\SourceContentFinder $content_finder
   *   The source content finder.
   * @param \Drupal\druki_content\Finder\RedirectSourceFileFinder $redirect_finder
   *   The redirect finder.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The system time.
   * @param \Drupal\Core\Queue\QueueWorkerManagerInterface $queue_worker
   *   The queue manager.
   */
  public function __construct(SourceContentFinder $content_finder, RedirectSourceFileFinder $redirect_finder, QueueFactory $queue_factory, StateInterface $state, TimeInterface $time, QueueWorkerManagerInterface $queue_worker) {
    $this->contentFinder = $content_finder;
    $this->redirectFinder = $redirect_finder;
    $this->queue = $queue_factory->get(self::QUEUE_NAME);
    $this->state = $state;
    $this->time = $time;
    $this->queueWorker = $queue_worker;
  }

  /**
   * Builds new queue from source directory.
   *
   * The previous queue will be cleared to ensure items will not duplicate each
   * over. It can happens when multiple builds was called during short period of
   * time.
   *
   * @param string $directory
   *   The with source content. This directory will be parsed on call.
   */
  public function buildFromPath(string $directory): void {
    // Clear queue to exclude duplicate work.
    $this->queue->deleteQueue();
    $this->addSourceContentList($directory);
    $this->addRedirectFileList($directory);
    $this->addCleanOperation();
  }

  /**
   * Builds new queue from source content list.
   *
   * @param string $directory
   *   The directory with content.
   */
  protected function addSourceContentList(string $directory): void {
    $source_content_list = $this->contentFinder->findAll($directory);
    if (!$source_content_list->numberOfItems()) {
      return;
    }

    $items_per_queue = Settings::get('entity_update_batch_size', 50);
    foreach ($source_content_list->chunk($items_per_queue) as $content_list_chunk) {
      $this->queue->createItem(new SourceContentListContentSyncQueueItem($content_list_chunk));
    }
  }

  /**
   * Adds redirect files into queue.
   *
   * @param string $directory
   *   The directory with content.
   */
  protected function addRedirectFileList(string $directory): void {
    $redirect_file_list = $this->redirectFinder->findAll($directory);
    if ($redirect_file_list->getIterator()->count() == 0) {
      return;
    }
    $redirect_queue_item = new ContentSyncRedirectQueueItem($redirect_file_list);
    $this->queue->createItem($redirect_queue_item);
  }

  /**
   * Adds clean operation into queue.
   */
  protected function addCleanOperation(): void {
    $sync_timestamp = $this->time->getRequestTime();
    $this->queue->createItem(new ContentSyncCleanQueueItem($sync_timestamp));
    $this->state->set('druki_content.last_sync_timestamp', $this->time->getRequestTime());
  }

  /**
   * Runs queue manually.
   *
   * @param int $time_limit
   *   The amount of seconds for this job.
   *
   * @return int
   *   The count of processed queue items.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
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
        // In case of any other kind of exception, log it and leave the item
        // in the queue to be processed again later.
        \watchdog_exception('druki_content', $e);
      }
    }

    return $count;
  }

  /**
   * Clears queue manually.
   */
  public function clear(): void {
    $this->queue->deleteQueue();
  }

}
