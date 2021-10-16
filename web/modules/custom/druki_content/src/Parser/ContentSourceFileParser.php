<?php

namespace Drupal\druki_content\Parser;

use Drupal\druki\Markdown\Parser\MarkdownParserInterface;
use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\druki_content\Sync\SourceContent\ParsedSourceContent;

/**
 * Provides parser for source content.
 *
 * This class will parse source content from file and convert it to structured
 * object to further use.
 */
final class ContentSourceFileParser {

  /**
   * The Markdown parser.
   */
  protected MarkdownParserInterface $markdownParser;

  /**
   * The HTML parser.
   */
  protected HtmlContentParser $htmlParser;

  /**
   * Constructs a new ContentSourceFileParser object.
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
   * @param \Drupal\druki_content\Data\ContentSourceFile $source_content
   *   The source content to parse.
   *
   * @return \Drupal\druki_content\Sync\SourceContent\ParsedSourceContent|null
   *   The parsed content. NULL if there is some problems with file.
   */
  public function parse(ContentSourceFile $source_content): ?ParsedSourceContent {
    if (!$source_content->isReadable()) {
      return NULL;
    }

    $content = $source_content->getContent();
    $html = $this->markdownParser->parse($content);
    $parsed_content = $this->htmlParser->parse($html, $source_content->getRealpath());

    return new ParsedSourceContent($source_content, $parsed_content);
  }

}
