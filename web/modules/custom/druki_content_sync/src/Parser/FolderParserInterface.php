<?php

namespace Drupal\druki_content_sync\Parser;

/**
 * Parser for filesystem folders.
 *
 * @package Drupal\druki_parser\Service
 */
interface FolderParserInterface {

  /**
   * Parses markdown content.
   *
   * @param string $directory
   *   Path to repository folder.
   *
   * @return array
   *   An array with files grouped by langcode.
   */
  public function parse($directory): array ;

}
