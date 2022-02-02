<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentNoteElement;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides note element parser.
 *
 * @deprecated Remove after content is updated to use <Aside>.
 */
final class ContentHtmlNoteElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool {
    if (!$element->hasAttribute('data-druki-note')) {
      return FALSE;
    }

    $note_type = $element->getAttribute('data-druki-note');
    $note_element = new ContentNoteElement($note_type);

    $crawler = new Crawler($element);
    $parser->parseChildren($crawler->html(), $context, $note_element);

    $context->getContent()->addElement($note_element);
    return TRUE;
  }

}
