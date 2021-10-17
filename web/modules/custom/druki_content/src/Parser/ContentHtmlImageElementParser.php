<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentHeadingElement;
use Drupal\druki_content\Data\ContentImageElement;
use Drupal\druki_content\Data\ContentParserContext;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphImage;
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
    $image = $crawler->filter('img')->first();
    if (!\count($image)) {
      return FALSE;
    }

    $alt = $image->attr('alt') ?? '';
    $image_element = new ContentImageElement($image->attr('src'), $alt);
    $context->getContent()->addElement($image_element);

    return TRUE;
  }

}
