<?php

declare(strict_types=1);

namespace Drupal\druki_author\Finder;

use Drupal\druki_author\Data\AuthorsFile;
use Symfony\Component\Finder\Finder;

/**
 * Provides finder for 'authors.json' file.
 */
final class AuthorsFileFinder implements AuthorsFileFinderInterface {

  /**
   * {@inheritdoc}
   */
  public function find(string $directory): ?AuthorsFile {
    if (!\is_dir($directory)) {
      return NULL;
    }
    $finder = new Finder();
    $finder->name('authors.json');
    $finder->depth(0);
    $finder->in($directory);
    if (!$finder->hasResults()) {
      return NULL;
    }
    $iterator = $finder->getIterator();
    $iterator->rewind();
    /** @var \Symfony\Component\Finder\SplFileInfo $first_file */
    $first_file = $iterator->current();
    return new AuthorsFile($first_file->getPathname());
  }

}
