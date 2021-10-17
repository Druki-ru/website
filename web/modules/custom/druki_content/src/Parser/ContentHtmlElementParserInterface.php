<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentParserContext;

/**
 * Provides interface for content HTML parser.
 */
interface ContentHtmlElementParserInterface {

  /**
   * Parses content from an element.
   *
   * @param \DOMElement $element
   *   The DOM element to process.
   * @param \Drupal\druki_content\Data\ContentParserContext $context
   *   The current parser context.
   * @param \Drupal\druki_content\Parser\ContentHtmlParser $parser
   *   The main HTML parser.
   *
   * @return bool
   *   TRUE if successfully processed current element, FALSE to let other
   *   parser to process it.
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool;

}
