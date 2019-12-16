<?php

namespace Drupal\druki_content\Synchronization\Queue;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\RequeueException;
use Drupal\Core\Queue\SuspendQueueException;
use Exception;

/**
 * Provide queue manager for content synchronization.
 */
class QueueManager {

  /**
   * The queue of processing content.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $queue;

  /**
   * The queue worker.
   *
   * @var \Drupal\Core\Queue\QueueWorkerManagerInterface
   */
  protected $queueWorkerManager;

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * SyncQueueManager constructor.
   *
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   * @param \Drupal\Core\Queue\QueueWorkerManagerInterface $queue_worker_manager
   *   The queue worker manager.
   */
  public function __construct(QueueFactory $queue_factory, QueueWorkerManagerInterface $queue_worker_manager) {
    $this->queue = $queue_factory->get('druki_content_sync');
    $this->queueWorkerManager = $queue_worker_manager;
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
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function run(int $time_limit = 15): int {
    /** @var \Drupal\Core\Queue\QueueWorkerInterface $queue_worker */
    $queue_worker = $this->queueWorkerManager->createInstance('druki_content_sync');

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
   * Gets queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   The queue object.
   */
  public function queue(): QueueInterface {
    return $this->queue;
  }

  /**
   * Clears queue manually.
   */
  public function clear(): void {
    $this->queue->deleteQueue();
  }

}
