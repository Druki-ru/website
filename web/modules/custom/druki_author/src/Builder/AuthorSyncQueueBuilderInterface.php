<?php

declare(strict_types=1);

namespace Drupal\druki_author\Builder;

/**
 * Defines an interface for author sync queue builders.
 */
interface AuthorSyncQueueBuilderInterface {

  /**
   * Builds queue from provided directory.
   *
   * @param string $directory
   *   The directory URI.
   *
   * @see \Drupal\druki_author\Finder\AuthorsFileFinder::find()
   */
  public function buildFromDirectory(string $directory): void;

}
