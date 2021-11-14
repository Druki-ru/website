<?php

declare(strict_types=1);

namespace Drupal\druki_author\Finder;

use Drupal\druki_author\Data\AuthorsFile;

/**
 * Defines an interface for authors.json file finders.
 */
interface AuthorsFileFinderInterface {

  /**
   * Search for authors file.
   *
   * The 'authors.json' file should be strictly inside provided directory. It
   * is not language specific file.
   *
   * @param string $directory
   *   The directory URI to search for a file.
   *
   * @return \Drupal\druki_author\Data\AuthorsFile|null
   *   The authors file object if found. NULL if not found.
   */
  public function find(string $directory): ?AuthorsFile;

}
