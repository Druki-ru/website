<?php

namespace Drupal\druki_parser\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\markdown\Markdown;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Finder\Finder;

/**
 * Parser for filesystem folders.
 *
 * This parser scans provided folder for specific structure:
 * - docs
 * -- LANGCODE - looking only for languages available on site. Folder must be
 *    the same name as langcode in Drupal.
 * --- All other files.
 *
 * @package Drupal\druki_parser\Service
 */
class DrukiFolderParser {

  /**
   * The finder component.
   *
   * @var \Symfony\Component\Finder\Finder
   */
  protected $finder;

  /**
   * DrukiFolderParser constructor.
   */
  public function __construct() {
    $this->finder = new Finder();
  }

  /**
   * {@inheritdoc}
   */
  public function parse($directory) {
    $this->finder->in($directory);
    $this->finder->name('*.md');
    $this->finder->name('*.MD');

    foreach ($this->finder as $file_info) {
      dump($file_info);
    }
  }

}
