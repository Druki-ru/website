<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\ExistingSite\Parser;

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
   * @covers ::parse()
   */
  public function testParser(): void {
    $html = '<p>Hello World!</p>';
    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');

    $content = new Content();
    $context = new ContentParserContext();
    $context->setContent($content);

    $html_parser = $this->container->get('druki_content.parser.content_html_parser');
    $parser = $this->container->get('druki_content.parser.content_html_text_element');
    foreach ($crawler->children() as $element) {
      $parser->parse($element, $context, $html_parser);
    }

    /** @var \Drupal\druki_content\Data\ContentTextElement $block */
    $block = $content->getElements()->offsetGet(0);
    $this->assertEquals($html, $block->getContent());
  }

}
