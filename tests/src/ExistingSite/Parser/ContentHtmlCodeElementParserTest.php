<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Parser;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentParserContext;
use Symfony\Component\DomCrawler\Crawler;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content html code element parser.
 *
 * @coversDefaultClass \Drupal\druki_content\Parser\ContentHtmlCodeElementParser
 */
final class ContentHtmlCodeElementParserTest extends ExistingSiteBase {

  /**
   * Tests parser.
   *
   * @covers ::parse()
   */
  public function testParser(): void {
    $html = '<pre><code>console.log(\'Hello World\');</code></pre>';

    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');

    $content = new Content();
    $context = new ContentParserContext();
    $context->setContent($content);

    $html_parser = $this->container->get('druki_content.parser.content_html_parser');
    $parser = $this->container->get('druki_content.parser.content_html_code_element');
    foreach ($crawler->children() as $element) {
      $parser->parse($element, $context, $html_parser);
    }

    /** @var \Drupal\druki_content\Data\ContentCodeElement $element */
    $element = $content->getElements()->offsetGet(0);
    $this->assertEquals("console.log('Hello World');", $element->getContent());
    $this->assertNull($element->getLanguage());

    $html = '<pre><code class="language-php">console.log(\'Hello World\');</code></pre>';

    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');

    $content = new Content();
    $context = new ContentParserContext();
    $context->setContent($content);
    foreach ($crawler->children() as $element) {
      $parser->parse($element, $context, $html_parser);
    }

    /** @var \Drupal\druki_content\Data\ContentCodeElement $element */
    $element = $content->getElements()->offsetGet(0);
    $this->assertEquals("console.log('Hello World');", $element->getContent());
    $this->assertEquals('php', $element->getLanguage());
  }

}
