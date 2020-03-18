<?php

namespace Drupal\druki_markdown\Parser;

use Drupal\druki_markdown\CommonMark\Extension\DrukiParserExtensions;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;

/**
 * Provides default Markdown parser.
 *
 * @todo consider is it need to be a service since it no more uses DI.
 */
class MarkdownParser implements MarkdownParserInterface {

  /**
   * The markdown parser.
   *
   * @var \Drupal\markdown\Plugin\Markdown\MarkdownParserInterface
   */
  protected $markdownParser;

  /**
   * Constructs a new MarkdownParser object.
   */
  public function __construct() {
    // The CommonMark used directly instead of 'markdown' service, because
    // the Markdown module and it's service require to have special text
    // filter and user permissions to use extensions. In our case this is
    // overhead and not needed, since we use parsed HTML in content and filter
    // become usless. But all other staff defined according to Markdown module
    // for correctly working with filter.
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
