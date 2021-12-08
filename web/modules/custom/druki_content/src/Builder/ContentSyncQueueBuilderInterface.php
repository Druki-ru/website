<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

/**
 * Defines an interface for content sync queue builder.
 */
interface ContentSyncQueueBuilderInterface {

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

}
