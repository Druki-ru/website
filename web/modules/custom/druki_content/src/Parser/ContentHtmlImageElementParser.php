<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentImageElement;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides image element parser.
 */
final class ContentHtmlImageElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool {
    if ($element->nodeName != 'img') {
      return FALSE;
    }
    $alt = $element->getAttribute('alt') ?? '';
    $image_element = new ContentImageElement($element->getAttribute('src'), $alt);
    $context->getContent()->addElement($image_element);
    return TRUE;
  }

}
