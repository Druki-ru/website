<?php

namespace Drupal\druki_parser\Service;

use Drupal\druki_paragraphs\Content\ContentStructure;

/**
 * Object for parse markdown and html and transform to specific data structures.
 *
 * @package Drupal\druki_parser\Service
 */
interface DrukiHTMLParserInterface {

  /**
   * Parses HTML to structured data.
   *
   * @param string $content
   *   The html content.
   *
   * @return \Drupal\druki_paragraphs\Content\ContentStructure
   *   The structured value object with content.
   */
  public function parse($content): ContentStructure;

}
