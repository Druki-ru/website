<?php

namespace Drupal\druki_parser\Service;

/**
 * Object for parse markdown and html and transform to specific data structures.
 *
 * @package Drupal\druki_parser\Service
 */
interface DrukiMarkdownParserInterface {

  /**
   * Parses markdown content.
   *
   * @param string $content
   *   The markdown content.
   *
   * @return string
   *   The HTML markup.
   */
  public function parse($content);

}
