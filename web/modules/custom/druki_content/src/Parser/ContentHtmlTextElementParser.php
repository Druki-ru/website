<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentParserContext;
use Drupal\druki_content\Data\ContentTextElement;

/**
 * Provides text element parser.
 */
final class ContentHtmlTextElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool {
    $html = $element->ownerDocument->saveHTML($element);
    $text_element = new ContentTextElement($html);
    $context->getContent()->addElement($text_element);
    return TRUE;
  }

}
