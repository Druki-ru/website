<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Queue;

/**
 * Provides interface for redirect sync queue manager.
 */
interface RedirectSyncQueueManagerInterface {

  /**
   * Builds queue from provided directories.
   *
   * @param array $directories
   *   An array with directories where to look for 'redirects.csv' file.
   *
   * @see \Drupal\druki_redirect\Finder\RedirectFileFinder
   */
  public function buildFromDirectories(array $directories): void;

  /**
   * Deletes everything related to the queue.
   */
  public function delete(): void;

}
