<?php

namespace Drupal\druki_content\Synchronization\Parser;

use Drupal\druki_content\Synchronization\Content\ContentStructure;

/**
 * Object for parse markdown and html and transform to specific data structures.
 */
interface HTMLParserInterface {

  /**
   * Parses HTML to structured data.
   *
   * @param string $content
   *   The html content.
   * @param null|string $filepath
   *   The filepath of parsed file. Will be used for internal links processing.
   *
   * @return \Drupal\druki_content\Synchronization\Content\ContentStructure
   *   The structured value object with content.
   */
  public function parse($content, $filepath = NULL): ContentStructure;

}
