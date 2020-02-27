<?php

namespace Drupal\druki_content\Sync;

use Drupal\Core\Queue\QueueFactory;
use Drupal\druki_content\Finder\SourceContentFinder;

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
   * @var \Drupal\druki_content\Finder\SourceContentFinder
   */
  protected $contentFinder;

  /**
   * The queue with synchronization items.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $queue;

  /**
   * Constructs a new SynchronizationQueueBuilder object.
   *
   * @param \Drupal\druki_content\Finder\SourceContentFinder $content_finder
   *   The source content finder.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   */
  public function __construct(SourceContentFinder $content_finder, QueueFactory $queue_factory) {
    $this->contentFinder = $content_finder;
    $this->queue = $queue_factory->get(static::QUEUE_NAME);
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
    $source_content = $this->contentFinder->findAll($directory);
    foreach ($source_content as $langcode => $source_content) {
      foreach ($source_content as $uri => $filename) {
        $sync_item = new SyncItem($uri, $langcode);
      }
    }
  }

}
