<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Builder;

/**
 * Defines an interface for redirect sync queue builder.
 */
interface RedirectSyncQueueBuilderInterface {

  /**
   * Builds queue item list from provided directories.
   *
   * @param array $directories
   *   An array with directories where to look for 'redirects.csv' file.
   *
   * @see \Drupal\druki_redirect\Finder\RedirectFileFinder
   */
  public function buildFromDirectories(array $directories): void;

}
