<?php

namespace Drupal\druki_content\ParsedContent;

use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provides class to load parsed content.
 */
final class ParsedContentLoader {

  /**
   * The list of found loaders sorted by 'priority'.
   *
   * @var \Drupal\druki_content\ParsedContent\ParsedContentItemLoaderInterface[]
   */
  protected $loaders = [];

  /**
   * Adds loader to the list.
   *
   * @param \Drupal\druki_content\ParsedContent\ParsedContentItemLoaderInterface $loader
   *   The loader instance.
   */
  public function addLoader(ParsedContentItemLoaderInterface $loader): void {
    $this->loaders[] = $loader;
  }

  /**
   * Process a single piece of data.
   *
   * @param mixed $data
   *   The data that must pe processed.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  public function process($data, DrukiContentInterface $content): void {
    foreach ($this->loaders as $loader) {
      if ($loader->supportsLoading($data)) {
        $loader->process($data, $content);
        break;
      }
    }
  }

}
