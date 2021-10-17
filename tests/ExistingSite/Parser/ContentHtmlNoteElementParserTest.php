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
 * @coversDefaultClass \Drupal\druki_content\Parser\ContentHtmlNoteElementParser
 */
final class ContentHtmlNoteElementParserTest extends ExistingSiteBase {

  /**
   * Tests parser.
   *
   * @covers ::parse()
   */
  public function testParser(): void {
    $html = <<<'HTML'
      <div data-druki-note="warning">
        <p>Hello World!</p>
        <img src="https://example.com/img.jpg" alt="Kitty Kitty!">
      </div>
    HTML;
    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');

    $content = new Content();
    $context = new ContentParserContext();
    $context->setContent($content);

    $html_parser = $this->container->get('druki_content.parser.content_html_parser');
    $parser = $this->container->get('druki_content.parser.content_html_note_element');
    foreach ($crawler->children() as $element) {
      $parser->parse($element, $context, $html_parser);
    }

    //dump($content);
  }

}
