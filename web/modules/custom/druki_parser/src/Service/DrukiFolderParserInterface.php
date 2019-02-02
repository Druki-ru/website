<?php

namespace Drupal\druki_parser\Service;

/**
 * Parser for filesystem folders.
 *
 * @package Drupal\druki_parser\Service
 */
interface DrukiFolderParserInterface {

  /**
   * Parses markdown content.
   *
   * @param string $directory
   *   Path to repository folder.
   *
   * @return array
   *   An array with files grouped by langcode.
   */
  public function parse($directory);

}
