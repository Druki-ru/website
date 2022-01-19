<?php

namespace Drupal\druki_content\Parser;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\druki\Markdown\Parser\MarkdownParserInterface;
use Drupal\druki_content\Data\ContentDocument;
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
   * A typed data manager.
   */
  protected TypedDataManagerInterface $typedDataManager;

  /**
   * Constructs a new ContentSourceFileParser object.
   *
   * @param \Drupal\druki\Markdown\Parser\MarkdownParserInterface $markdown_parser
   *   The Markdown parser.
   * @param \Drupal\druki_content\Parser\ContentHtmlParser $html_parser
   *   The HTML parser.
   * @param \Drupal\Core\TypedData\TypedDataManagerInterface $typed_data_manager
   *   A typed data manager.
   */
  public function __construct(MarkdownParserInterface $markdown_parser, ContentHtmlParser $html_parser, TypedDataManagerInterface $typed_data_manager) {
    $this->markdownParser = $markdown_parser;
    $this->htmlParser = $html_parser;
    $this->typedDataManager = $typed_data_manager;
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

    $content_metadata_definition = $this->typedDataManager->createDataDefinition('druki_content_documentation_metadata');
    $content_metadata = $this->typedDataManager->create($content_metadata_definition, $front_matter->getData());
    if ($content_metadata->validate()->count()) {
      return NULL;
    }

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
