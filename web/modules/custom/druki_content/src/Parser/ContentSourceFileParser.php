<?php

namespace Drupal\druki_content\Parser;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\druki\Markdown\Parser\MarkdownParserInterface;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\druki_content\Data\ContentMetadata;
use Drupal\druki_content\Data\ContentParserContext;
use Drupal\druki_content\Data\ContentSourceFile;

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
  protected ContentHtmlParser $htmlParser;

  /**
   * Constructs a new ContentSourceFileParser object.
   *
   * @param \Drupal\druki\Markdown\Parser\MarkdownParserInterface $markdown_parser
   *   The Markdown parser.
   * @param \Drupal\druki_content\Parser\ContentHtmlParser $html_parser
   *   The HTML parser.
   */
  public function __construct(MarkdownParserInterface $markdown_parser, ContentHtmlParser $html_parser) {
    $this->markdownParser = $markdown_parser;
    $this->htmlParser = $html_parser;
  }

  /**
   * Parse content from its source to structured format.
   *
   * This will read file, make all conversions and wrap result to structured
   * value object suitable to consume.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFile $content_file
   *   The source content to parse.
   *
   * @return \Drupal\druki_content\Data\ContentDocument|null
   *   The content document. NULL if there is some problems with file.
   */
  public function parse(ContentSourceFile $content_file): ?ContentDocument {
    if (!$content_file->isReadable()) {
      return NULL;
    }

    $context = new ContentParserContext();
    $context->setContentSourceFile($content_file);

    $front_matter = new FrontMatter($content_file->getContent());
    $content_metadata = ContentMetadata::createFromArray($front_matter->getData());
    $content_markdown = $front_matter->getContent();
    $content_html = $this->markdownParser->parse($content_markdown);
    $content = $this->htmlParser->parse($content_html, $context);

    return new ContentDocument(
      $content_file->getLanguage(),
      $content_metadata,
      $content,
    );
  }

}
