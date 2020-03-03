<?php

namespace Drupal\druki_content\ParsedContent;

use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provided interface for parsed content item loaders.
 */
interface ParsedContentItemLoaderInterface {

  /**
   * Checks whether the given class is supported for processing by this loader.
   *
   * @param mixed $data
   *   The object to process.
   *
   * @return bool
   *   TRUE if supported, FALSE otherwise.
   */
  public function supportsLoading($data): bool;

  /**
   * Process the data.
   *
   * @param mixed $data
   *   The data to process.
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The destination content.
   */
  public function process($data, DrukiContentInterface $content): void;

}
