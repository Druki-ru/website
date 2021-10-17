<?php

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Parse HTML markup to structured value objects.
 */
final class ContentHtmlParser {

  /**
   * An array with element parsers.
   */
  protected array $elementParsers = [];

  /**
   * Adds element parser.
   *
   * @param \Drupal\druki_content\Parser\ContentHtmlElementParserInterface $element_parser
   *   The element parser instance.
   */
  public function addElementParser(ContentHtmlElementParserInterface $element_parser): void {
    $this->elementParsers[] = $element_parser;
  }

  /**
   * Parse HTML string into structured content.
   *
   * @param string $html
   *   The HTML to parse.
   * @param \Drupal\druki_content\Data\ContentParserContext|null $context
   *   The parser context.
   *
   * @return \Drupal\druki_content\Data\Content
   *   The structured content.
   */
  public function parse(string $html, ?ContentParserContext $context = NULL): Content {
    $content = new Content();
    if (!$context) {
      $context = new ContentParserContext();
    }
    $context->setContent($content);

    $crawler = new Crawler($html);
    // Move to body. We expect content here.
    $crawler = $crawler->filter('body');
    foreach ($crawler->children() as $element) {
      /** @var \Drupal\druki_content\Parser\ContentHtmlElementParserInterface $element_parser */
      foreach ($this->elementParsers as $element_parser) {
        if ($element_parser->parse($element, $context, $this)) {
          // If element successfully parsed, move to another element.
          continue 2;
        }
      }
    }
    return $content;
  }

  /**
   * Parses internal links to another markdown files.
   *
   * @param \DOMNode $dom_element
   *   The DOM element to process.
   * @param string $filepath
   *   The filepath of file in which this link was found.
   *
   * @todo Add preprocess for parser.
   */
  protected function processInternalLink(\DOMNode $dom_element, string $filepath): void {
    if (empty($dom_element->childNodes)) {
      return;
    }

    /** @var \DOMElement $child_node */
    foreach ($dom_element->childNodes as $child_node) {

      if ($child_node->nodeName == 'a') {
        $href = $child_node->getAttribute('href');

        // Must end up with Markdown extension: .md, .MD.
        if (!\preg_match("/.*\.md$/mi", $href)) {
          continue;
        }

        $child_node->setAttribute('data-druki-internal-link-filepath', $filepath);
      }

      $this->processInternalLink($child_node, $filepath);
    }
  }

}
