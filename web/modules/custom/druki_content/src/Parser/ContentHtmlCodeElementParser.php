<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentCodeElement;
use Drupal\druki_content\Data\ContentParserContext;

/**
 * Provides code element parser.
 */
final class ContentHtmlCodeElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool {
    $node_name = $element->nodeName;
    if ($node_name != 'pre') {
      return FALSE;
    }
    $html = $element->ownerDocument->saveHTML($element);
    $code_element = new ContentCodeElement($html);
    $context->getContent()->addElement($code_element);
    return TRUE;
  }

}
