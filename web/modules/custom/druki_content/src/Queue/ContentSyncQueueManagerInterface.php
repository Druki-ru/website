<?php

declare(strict_types=1);

namespace Drupal\druki_content\Queue;

use Drupal\Core\Queue\QueueInterface;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;

/**
 * Provides interface for content sync queue manager.
 */
interface ContentSyncQueueManagerInterface {

  /**
   * The queue name used for synchronisation.
   */
  public const QUEUE_NAME = 'druki_content_sync';

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
  public function buildFromPath(string $directory): void;

  /**
   * Clears queue manually.
   */
  public function delete(): void;

  /**
   * Gets queue state.
   *
   * @return \Drupal\druki\Repository\EntitySyncQueueStateInterface
   *   The queue state storage.
   */
  public function getState(): EntitySyncQueueStateInterface;

  /**
   * Gets queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   The queue.
   */
  public function getQueue(): QueueInterface;

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
   *
   * @todo Create separate service for this.
   */
  public function run(int $time_limit = 15): int;

}
