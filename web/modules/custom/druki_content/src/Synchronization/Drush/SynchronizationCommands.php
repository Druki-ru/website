<?php

namespace Drupal\druki_content\Synchronization\Drush;

use Drupal\Component\Utility\Environment;
use Drupal\Component\Utility\Timer;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\druki_content\Synchronization\Queue\QueueManager;
use Drush\Commands\DrushCommands;

/**
 * Provides synchronization commands for drush.
 */
class SynchronizationCommands extends DrushCommands {

  /**
   * The sync queue manager.
   *
   * @var \Drupal\druki_content\Synchronization\Queue\QueueManager
   */
  protected $queueManager;

  /**
   * The lock.
   *
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  /**
   * Constructs a new SynchronizationCommands object.
   *
   * @param \Drupal\druki_content\Synchronization\Queue\QueueManager $queue_manager
   *   The queue manager.
   * @param \Drupal\Core\Lock\LockBackendInterface $lock_backend
   *   The lock.
   */
  public function __construct(QueueManager $queue_manager, LockBackendInterface $lock_backend) {
    parent::__construct();
    $this->queueManager = $queue_manager;
    $this->lock = $lock_backend;
  }

  /**
   * Runs synchronization queue.
   *
   * This command is intended for run queue in separate process from cron.
   *
   * @command druki-content:synchronization-process
   * @option time-limit The maximum number of seconds allowed to run the queue
   */
  public function process($options = ['time-limit' => self::OPT]) {
    // Allow execution to continue even if the request gets cancelled.
    @ignore_user_abort(TRUE);
    $time_limit = !empty($options['time-limit']) ? $options['time-limit'] : 240;
    // Try to acquire lock.
    if (!$this->lock->acquire('druki-content:synchronization-process', $time_limit)) {
      // Process is still running normally.
      $this
        ->logger()
        ->warning('Attempting to re-run synchronization while it is already running.');
    }
    else {
      // Try to allocate enough time to run as much as possible for single run.
      Environment::setTimeLimit($time_limit);
      Timer::start('druki-content:synchronization-process');
      $count = $this->queueManager->run($time_limit);
      $elapsed = Timer::stop('druki-content:synchronization-process')['time'] / 1000;
      $this->lock->release('druki-content:synchronization-process');
      $this
        ->logger()
        ->success(dt('Synchronized @count items in @elapsed sec.', [
          '@count' => $count,
          '@elapsed' => round($elapsed, 2),
        ]));
    }
  }

}
