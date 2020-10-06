<?php

namespace Drupal\druki_content\Sync\SourceContent;

use Drupal\druki\Markdown\Parser\MarkdownParserInterface;
use Drupal\druki_content\Sync\Parser\HtmlContentParser;

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
   * @var \Drupal\druki\Markdown\Parser\MarkdownParserInterface
   */
  protected $markdownParser;

  /**
   * The HTML parser.
   *
   * @var \Drupal\druki_content\Sync\Parser\HtmlContentParser
   */
  protected $htmlParser;

  /**
   * Constructs a new SourceContentParser object.
   *
   * @param \Drupal\druki\Markdown\Parser\MarkdownParserInterface $markdown_parser
   *   The markdown parser.
   */
  public function __construct(MarkdownParserInterface $markdown_parser) {
    $this->markdownParser = $markdown_parser;
    $this->htmlParser = new HtmlContentParser();
  }

  /**
   * Parse content from its source to structured format.
   *
   * This will read file, make all conversions and wrap result to structured
   * value object suitable to consume.
   *
   * @param \Drupal\druki_content\Sync\SourceContent\SourceContent $source_content
   *   The source content to parse.
   *
   * @return \Drupal\druki_content\Sync\SourceContent\ParsedSourceContent|null
   *   The parsed content. NULL if there is some problems with file.
   */
  public function parse(SourceContent $source_content): ?ParsedSourceContent {
    if (!$source_content->isReadable()) {
      return NULL;
    }

    $content = $source_content->getContent();
    $html = $this->markdownParser->parse($content);
    $parsed_content = $this->htmlParser->parse($html, $source_content->getRealpath());

    return new ParsedSourceContent($source_content, $parsed_content);
  }

}
