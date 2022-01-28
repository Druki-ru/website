<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\ExistingSite\Parser;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content html image element parser.
 *
 * @coversDefaultClass \Drupal\druki_content\Parser\ContentHtmlImageElementParser
 */
final class ContentHtmlImageElementParserTest extends ExistingSiteBase {

  /**
   * Tests parser.
   *
   * @covers ::parse()
   */
  public function testParser(): void {
    $html = '<p><img src="https://example.com/img.jpg" alt="Hello World!"></p>';
    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');

    $content = new Content();
    $context = new ContentParserContext();
    $context->setContent($content);

    $html_parser = $this->container->get('druki_content.parser.content_html_parser');
    $parser = $this->container->get('druki_content.parser.content_html_image_element');
    foreach ($crawler->children() as $element) {
      $parser->parse($element, $context, $html_parser);
    }

    /** @var \Drupal\druki_content\Data\ContentImageElement $element */
    $element = $content->getElements()->offsetGet(0);
    $this->assertEquals('https://example.com/img.jpg', $element->getSrc());
    $this->assertEquals('Hello World!', $element->getAlt());
  }

}
