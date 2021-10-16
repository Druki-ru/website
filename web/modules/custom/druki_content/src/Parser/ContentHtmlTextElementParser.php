<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentParserContext;
use Drupal\druki_content\Data\ContentTextBlock;

/**
 * Provides text element parser.
 */
final class ContentHtmlTextElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context): bool {
    $html = $element->ownerDocument->saveHTML($element);
    $block = new ContentTextBlock($html);
    $context->getContent()->addBlock($block);
    return TRUE;
  }

}
