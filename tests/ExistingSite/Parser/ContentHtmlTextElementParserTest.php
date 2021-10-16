<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Parser;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content html text element parser.
 *
 * @coversDefaultClass \Drupal\druki_content\Parser\ContentHtmlTextElementParser
 */
final class ContentHtmlTextElementParserTest extends ExistingSiteBase {

  /**
   * Tests parser.
   *
   * @return void
   *
   * @covers ::parse()
   */
  public function testParser(): void {
    $html = '<p>Hello World!</p>';
    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');

    $content = new Content();
    $context = new ContentParserContext();
    $context->setContent($content);

    $parser = $this->container->get('druki_content.parser.content_html_text_element');
    $parser->parse($crawler->children()->getNode(0), $context);

    /** @var \Drupal\druki_content\Data\ContentTextBlock $block */
    $block = $content->getBlocks()->offsetGet(0);
    $this->assertEquals($html, $block->getContent());
  }

}
