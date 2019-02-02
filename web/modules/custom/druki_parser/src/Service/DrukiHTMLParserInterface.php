<?php

namespace Drupal\druki_parser\Service;

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
   * @return array
   *   An array with structured data.
   */
  public function parse($content);

}
