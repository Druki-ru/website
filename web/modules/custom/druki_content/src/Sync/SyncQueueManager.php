<?php

namespace Drupal\druki_content\Sync;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\RequeueException;
use Drupal\Core\Queue\SuspendQueueException;
use Drupal\Core\Site\Settings;
use Drupal\Core\State\StateInterface;
use Drupal\druki_content\SourceContent\SourceContentFinder;
use Drupal\druki_content\SourceContent\SourceContentList;
use Exception;

/**
 * Provides queue manager for synchronization content.
 */
final class SyncQueueManager {

  /**
   * The queue name used for synchronisation.
   */
  public const QUEUE_NAME = 'druki_content_sync';

  /**
   * The source content finder.
   *
   * @var \Drupal\druki_content\SourceContent\SourceContentFinder
   */
  protected $contentFinder;

  /**
   * The queue with synchronization items.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $queue;

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The system time.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The queue worker.
   *
   * @var \Drupal\Core\Queue\QueueWorkerManagerInterface
   */
  protected $queueWorker;

  /**
   * Constructs a new SynchronizationQueueBuilder object.
   *
   * @param \Drupal\druki_content\SourceContent\SourceContentFinder $content_finder
   *   The source content finder.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The system time.
   */
  public function __construct(SourceContentFinder $content_finder, QueueFactory $queue_factory, StateInterface $state, TimeInterface $time, QueueWorkerManagerInterface $queue_worker) {
    $this->contentFinder = $content_finder;
    $this->queue = $queue_factory->get(static::QUEUE_NAME);
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
    $content_list = $this->contentFinder->findAll($directory);
    $this->buildFromSourceContentList($content_list);
  }

  /**
   * Builds new queue from source content list.
   *
   * @param \Drupal\druki_content\SourceContent\SourceContentList $source_content_list
   *   The source content list.
   */
  public function buildFromSourceContentList(SourceContentList $source_content_list) {
    if (!$source_content_list->numberOfItems()) {
      return;
    }

    // Clear queue to exclude duplicate work.
    $this->queue->deleteQueue();
    $items_per_queue = Settings::get('entity_update_batch_size', 50);
    foreach ($source_content_list->chunk($items_per_queue) as $content_list_chunk) {
      $this->queue->createItem(new SyncQueueItem(SyncQueueItem::SYNC, $content_list_chunk));
    }

    $sync_timestamp = $this->time->getRequestTime();
    $this->queue->createItem(new SyncQueueItem(SyncQueueItem::CLEAN, $sync_timestamp));
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
    $end = time() + $time_limit;
    $lease_time = $time_limit;
    $count = 0;
    while (time() < $end && ($item = $this->queue->claimItem($lease_time))) {
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
      catch (Exception $e) {
        // In case of any other kind of exception, log it and leave the item
        // in the queue to be processed again later.
        watchdog_exception('druki_content', $e);
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
