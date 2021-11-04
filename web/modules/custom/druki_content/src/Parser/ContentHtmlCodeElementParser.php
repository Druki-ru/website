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
    $code = $crawler->filter('pre > code');
    $language = NULL;
    /** @var \DOMElement $code_element */
    if ($code_element = $code->getIterator()->current()) {
      if ($code_element->hasAttribute('class')) {
        $classes = \explode(' ', $code_element->getAttribute('class'));
        foreach ($classes as $class) {
          if (\preg_match('/language-(.+)/', $class, $matches)) {
            $language = $matches[1];
            break;
          }
        }
      }
    }
    $code_element = new ContentCodeElement($code->html(), $language);
    $context->getContent()->addElement($code_element);
    return TRUE;
  }

}
