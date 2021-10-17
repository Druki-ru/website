<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentHeadingElement;
use Drupal\druki_content\Data\ContentParserContext;

/**
 * Provides text element parser.
 */
final class ContentHtmlHeadingElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool {
    $node_name = $element->nodeName;
    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
    if (!\in_array($node_name, $heading_elements)) {
      return FALSE;
    }

    $level = match ($element->nodeName) {
      'h1' => 1,
      'h2' => 2,
      'h3' => 3,
      'h4' => 4,
      'h5' => 5,
      'h6' => 6,
    // @codingStandardsIgnoreStart
    // Drupal Coding Standards fails to validate 'match' statement.
    };
    // @codingStandardsIgnoreEnd
    $heading_element = new ContentHeadingElement($level, $element->textContent);
    $context->getContent()->addElement($heading_element);
    return TRUE;
  }

}
