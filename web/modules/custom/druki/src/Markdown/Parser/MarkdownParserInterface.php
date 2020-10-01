<?php

namespace Drupal\druki\Markdown\Parser;

/**
 * Object for parse markdown and html and transform to specific data structures.
 *
 * @package Drupal\druki_parser\Service
 */
interface MarkdownParserInterface {

  /**
   * Parses markdown content.
   *
   * @param string $content
   *   The markdown content.
   *
   * @return string
   *   The HTML markup.
   */
  public function parse(string $content): string;

}
