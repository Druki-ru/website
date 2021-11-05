<?php

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentElementInterface;
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
   * An array with HTML preprocessors.
   */
  protected array $htmlPreprocessors = [];

  /**
   * Indicates should HTML be preprocessed.
   */
  protected bool $preprocess = TRUE;

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
   * Adds HTML preprocessor.
   *
   * @param \Drupal\druki_content\Parser\ContentHtmlPreprocessorInterface $preprocessor
   *   The HTML preprocessor.
   */
  public function addHtmlPreprocessor(ContentHtmlPreprocessorInterface $preprocessor): void {
    $this->htmlPreprocessors[] = $preprocessor;
  }

  /**
   * Parse children content.
   *
   * @param string $html
   *   The children HTML markup.
   * @param \Drupal\druki_content\Data\ContentParserContext $context
   *   The parent context.
   * @param \Drupal\druki_content\Data\ContentElementInterface $parent
   *   The parent element.
   */
  public function parseChildren(string $html, ContentParserContext $context, ContentElementInterface $parent): void {
    // Disable HTML preprocessing, since it was done for main HTML from which
    // these current part was taken.
    $this->preprocess = FALSE;
    $children_content = $this->parse($html, clone $context);
    $this->preprocess = TRUE;
    /** @var \Drupal\druki_content\Data\ContentElementInterface $child_element */
    foreach ($children_content->getElements() as $child_element) {
      $child_element->setParent($parent);
      $parent->addChild($child_element);
    }
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
    if (!$context) {
      $context = new ContentParserContext();
    }
    $content = new Content();
    $context->setContent($content);

    if ($this->preprocess) {
      $html = $this->preprocess($html, $context);
    }

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
   * Preprocess HTML.
   *
   * @param string $html
   *   The source HTML.
   * @param \Drupal\druki_content\Data\ContentParserContext $context
   *   The content parser context.
   *
   * @return string
   *   The processed HTML.
   */
  protected function preprocess(string $html, ContentParserContext $context): string {
    /** @var \Drupal\druki_content\Parser\ContentHtmlPreprocessorInterface $preprocessor */
    foreach ($this->htmlPreprocessors as $preprocessor) {
      $html = $preprocessor->preprocess($html, $context);
    }
    return $html;
  }

}
