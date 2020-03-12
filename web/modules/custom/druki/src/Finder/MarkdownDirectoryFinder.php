<?php

namespace Drupal\druki\Finder;

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
   * @return \Symfony\Component\Finder\SplFileInfo[]
   *   An array with founded files.
   *
   * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
   */
  public function findAll(): array {
    $all = [];

    $this->finder->in($this->directories);
    if ($this->finder->hasResults()) {
      foreach ($this->finder as $file_info) {
        $all[] = $file_info;
      }
    }

    return $all;
  }

}
