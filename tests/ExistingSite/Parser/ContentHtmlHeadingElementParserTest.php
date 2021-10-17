<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Parser;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content html heading element parser.
 *
 * @coversDefaultClass \Drupal\druki_content\Parser\ContentHtmlHeadingElementParser
 */
final class ContentHtmlHeadingElementParserTest extends ExistingSiteBase {

  /**
   * Tests parser.
   *
   * @covers ::parse()
   */
  public function testParser(): void {
    $html = '<h1>Hello first!</h1><h2>Hello second!</h2>';
    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');

    $content = new Content();
    $context = new ContentParserContext();
    $context->setContent($content);

    $html_parser = $this->container->get('druki_content.parser.content_html_parser');
    $parser = $this->container->get('druki_content.parser.content_html_heading_element');
    foreach ($crawler->children() as $element) {
      $parser->parse($element, $context, $html_parser);
    }

    /** @var \Drupal\druki_content\Data\ContentHeadingElement $block */
    $block = $content->getElements()->offsetGet(0);
    $this->assertEquals(1, $block->getLevel());
    $this->assertEquals('Hello first!', $block->getContent());

    $block = $content->getElements()->offsetGet(1);
    $this->assertEquals(2, $block->getLevel());
    $this->assertEquals('Hello second!', $block->getContent());
  }

}
