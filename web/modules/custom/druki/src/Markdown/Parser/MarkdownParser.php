<?php

namespace Drupal\druki\Markdown\Parser;

use Drupal\druki\Markdown\CommonMark\Extension\DrukiParserExtensions;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;

/**
 * Provides default Markdown parser.
 *
 * Defined as service for only one instance per request.
 */
final class MarkdownParser implements MarkdownParserInterface {

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
