<?php

namespace Drupal\druki_parser\Service;

use Drupal\markdown\Markdown;

/**
 * Object for parse markdown and html and transform to specific data structures.
 *
 * @package Drupal\druki_parser\Service
 */
class DrukiParser implements DrukiParserInterface {

  /**
   * The markdown parser.
   *
   * @var \Drupal\markdown\Plugin\Markdown\MarkdownParserInterface
   */
  protected $markdownParser;

  /**
   * DrukiParser constructor.
   *
   * @param \Drupal\markdown\Markdown $markdown
   *   The markdown service.
   */
  public function __construct(Markdown $markdown) {
    $this->markdownParser = $markdown->getParser('thephpleague/commonmark', 'markdown');
  }

  /**
   * Parses markdown content.
   *
   * @param string $content
   *   The markdown content.
   *
   * @return string
   *   The HTML markup.
   */
  public function parseMarkdown($content) {
    return $this->markdownParser->convertToHtml($content);
  }

}
