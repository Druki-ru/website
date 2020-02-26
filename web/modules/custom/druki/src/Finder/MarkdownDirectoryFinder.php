<?php

namespace Drupal\druki\Finder;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

/**
 * Provides Markdown files finder.
 */
final class MarkdownDirectoryFinder {

  /**
   * An array of directories to scan.
   *
   * @var array
   */
  protected $directories;

  /**
   * The finder.
   *
   * @var \Symfony\Component\Finder\Finder
   */
  protected $finder;

  /**
   * Constructs a new MarkdownDirectoryFinder object.
   *
   * @param array $directories
   *   An array of directories to scan.
   */
  public function __construct(array $directories) {
    $this->finder = new Finder();
    $this->finder->name('*.md');
    $this->finder->name('*.MD');
    $this->directories = $directories;
  }

  /**
   * Scans directories for Markdown files.
   *
   * @return array
   *   An array with founded files keyed by pathname.
   */
  public function findAll() {
    $all = [];

    foreach ($this->directories as $directory) {
      try {
        $this->finder->in($directory);
      }
      catch (DirectoryNotFoundException $e) {
        continue;
      }
      foreach ($this->finder as $file_info) {
        $all[$file_info->getPathname()] = $file_info->getFilename();
      }
    }

    return $all;
  }

}
