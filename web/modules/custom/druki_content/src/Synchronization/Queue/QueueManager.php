<?php

namespace Drupal\druki_content\Synchronization\Queue;

use Drupal\Core\Queue\QueueFactory;
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
   */
  public function run(): void {
    $queue_worker_definition = $this->queueWorkerManager->getDefinition('druki_content_sync');
    /** @var \Drupal\Core\Queue\QueueWorkerInterface $queue_worker */
    $queue_worker = $this->queueWorkerManager->createInstance('druki_content_sync');

    if (isset($queue_worker_definition['cron'])) {
      // Make sure every queue exists. There is no harm in trying to recreate
      // an existing queue.
      $this->queue->createQueue();

      $end = time() + (isset($info['cron']['time']) ? $queue_worker_definition['cron']['time'] : 15);
      $lease_time = isset($info['cron']['time']) ?: NULL;
      while (time() < $end && ($item = $this->queue->claimItem($lease_time))) {
        try {
          $queue_worker->processItem($item->data);
          $this->queue->deleteItem($item);
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
    }
  }

  /**
   * Clears queue manually.
   */
  public function clear(): void {
    $this->queue->deleteQueue();
  }

}
