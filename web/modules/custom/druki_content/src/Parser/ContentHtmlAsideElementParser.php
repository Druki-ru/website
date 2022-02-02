<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentAsideElement;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides parser for '<Aside>' markdown elements.
 *
 * An '<Aside>' markdown element converted into '<aside>' HTML element so this
 * parser looking for it.
 *
 * Aside element contains HTML inside it as a content, so it should be parsed
 * as well.
 *
 * @see \Drupal\druki\Markdown\CommonMark\Block\Renderer\AsideRenderer
 */
final class ContentHtmlAsideElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool {
    if ($element->tagName != 'aside') {
      return FALSE;
    }

    $aside_type = $element->getAttribute('data-type');
    $aside_header = NULL;
    if ($element->hasAttribute('data-header')) {
      $aside_header = $element->getAttribute('data-header');
    }

    $aside_element = new ContentAsideElement($aside_type, $aside_header);

    $crawler = new Crawler($element);
    $parser->parseChildren($crawler->html(), $context, $aside_element);

    $context->getContent()->addElement($aside_element);
    return TRUE;
  }

}
