<?php

namespace Drupal\druki_content\SourceContent;

use Drupal\druki_content\ParsedContent\ParsedContent;
use Drupal\druki_content\Parser\HtmlContentParser;
use Drupal\druki_markdown\Parser\MarkdownParserInterface;

/**
 * Provides parser for source content.
 *
 * This class will parse source content from file and convert it to structured
 * object to further use.
 */
final class SourceContentParser {

  /**
   * The Markdown parser.
   *
   * @var \Drupal\druki_markdown\Parser\MarkdownParserInterface
   */
  protected $markdownParser;

  /**
   * The HTML parser.
   *
   * @var \Drupal\druki_content\Parser\HtmlContentParser
   */
  protected $htmlParser;

  /**
   * Constructs a new SourceContentParser object.
   *
   * @param \Drupal\druki_markdown\Parser\MarkdownParserInterface $markdown_parser
   *   The markdown parser.
   */
  public function __construct(MarkdownParserInterface $markdown_parser) {
    $this->markdownParser = $markdown_parser;
    $this->htmlParser = new HtmlContentParser();
  }

  public function parse(SourceContent $source_content): ?ParsedContent {
    if (!$source_content->isReadable()) {
      return NULL;
    }

    $content = $source_content->getContent();
    $html = $this->markdownParser->parse($content);
    return $this->htmlParser->parse($html);
  }

}
