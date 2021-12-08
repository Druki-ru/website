<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Finder;

use Drupal\druki_redirect\Data\RedirectFileList;

/**
 * Defines an interface for redirect file finder.
 */
interface RedirectFileFinderInterface {

  /**
   * Search for redirect files.
   *
   * @param array $directories
   *   An array with directories to looking for redirects.
   *
   * @return \Drupal\druki_redirect\Data\RedirectFileList
   *   The list with redirect files.
   */
  public function findAll(array $directories): RedirectFileList;

}
