<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentCodeElement;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides code element parser.
 */
final class ContentHtmlCodeElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool {
    if ($element->nodeName != 'pre') {
      return FALSE;
    }
    $crawler = new Crawler($element);
    $code = $crawler->filter('pre > code')->html();
    $code_element = new ContentCodeElement($code);
    $context->getContent()->addElement($code_element);
    return TRUE;
  }

}
