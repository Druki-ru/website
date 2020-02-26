<?php

namespace Drupal\druki_content\Synchronization\Parser;

/**
 * Parser for filesystem folders.
 *
 * @deprecated In flavor of \Drupal\druki_content\Finder\SourceContentFinder
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
  public function parse($directory): array;

}
