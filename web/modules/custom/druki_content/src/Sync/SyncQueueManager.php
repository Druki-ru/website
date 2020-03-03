<?php

namespace Drupal\druki_content\Sync;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Site\Settings;
use Drupal\Core\State\StateInterface;
use Drupal\druki_content\SourceContent\SourceContentFinder;
use Drupal\druki_content\SourceContent\SourceContentList;

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
  public function __construct(SourceContentFinder $content_finder, QueueFactory $queue_factory, StateInterface $state, TimeInterface $time) {
    $this->contentFinder = $content_finder;
    $this->queue = $queue_factory->get(static::QUEUE_NAME);
    $this->state = $state;
    $this->time = $time;
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

}
