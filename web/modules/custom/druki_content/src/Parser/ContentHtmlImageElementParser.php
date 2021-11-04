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
    $crawler = new Crawler($element);
    // The <img> - is inline element and must be inside any other block element.
    // The Markdown do it for ourself and wrap it by '<p>'. So it's expected to
    // find images inside <p>.
    if (!$crawler->matches('p > img')) {
      return FALSE;
    }
    $image = $crawler->filter('p > img')->getNode(0);
    $alt = $image->getAttribute('alt') ?? '';
    $image_element = new ContentImageElement($image->getAttribute('src'), $alt);
    $context->getContent()->addElement($image_element);
    return TRUE;
  }

}
