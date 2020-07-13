<?php

namespace Drupal\druki_markdown\Parser;

use Drupal\druki_markdown\CommonMark\Extension\DrukiParserExtensions;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;

/**
 * Provides default Markdown parser.
 */
class MarkdownParser implements MarkdownParserInterface {

  /**
   * The markdown parser.
   *
   * @var \League\CommonMark\CommonMarkConverter
   */
  protected $markdownParser;

  /**
   * Constructs a new MarkdownParser object.
   */
  public function __construct() {
    $environment = Environment::createCommonMarkEnvironment();
    $environment->addExtension(new DrukiParserExtensions());
    $this->markdownParser = new CommonMarkConverter([], $environment);
  }

  /**
   * {@inheritdoc}
   */
  public function parse(string $content): string {
    return $this->markdownParser->convertToHtml($content);
  }

}
