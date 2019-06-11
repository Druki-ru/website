<?php

namespace Drupal\druki_parser\Service;


use Drupal\druki_content_sync\Content\ContentStructure;

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
   * @param null|string $filepath
   *   The filepath of parsed file. Will be used for internal links processing.
   *
   * @return \Drupal\druki_content_sync\Content\ContentStructure The structured value object with content.
   *   The structured value object with content.
   */
  public function parse($content, $filepath = NULL): ContentStructure;

}
