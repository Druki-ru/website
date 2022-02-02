<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Kernel\Parser;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentAsideElement;
use Drupal\druki_content\Data\ContentParserContext;
use Drupal\druki_content\Parser\ContentHtmlAsideElementParser;
use Drupal\druki_content\Parser\ContentHtmlParser;
use Drupal\Tests\druki_content\Kernel\DrukiContentKernelTestBase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides test for ContentHtmlAsideElement HTML parser.
 *
 * @coversDefaultClass \Drupal\druki_content\Parser\ContentHtmlAsideElementParser
 */
final class ContentHtmlAsideElementParserTest extends DrukiContentKernelTestBase {

  /**
   * The content HTML parser.
   */
  protected ?ContentHtmlParser $contentHtmlParser;

  /**
   * The content HTML aside element parser.
   */
  protected ?ContentHtmlAsideElementParser $asideElementParser;

  /**
   * Tests that parser works as expected.
   *
   * @dataProvider asideHtmlProvider
   */
  public function testParser(string $html, ?string $expected_element, ?string $expected_type, ?string $expected_title): void {
    $content = new Content();
    $context = new ContentParserContext();
    $context->setContent($content);

    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');
    foreach ($crawler->children() as $element) {
      $this->asideElementParser->parse($element, $context, $this->contentHtmlParser);
    }

    if (!$expected_element) {
      $this->assertEquals(0, $content->getElements()->count());
    }
    else {
      $parsed_element = $content->getElements()->offsetGet(0);
      \assert($parsed_element instanceof ContentAsideElement);
      $this->assertInstanceOf($expected_element, $parsed_element);
      $this->assertEquals($expected_type, $parsed_element->getType());
      $this->assertEquals($expected_title, $parsed_element->getHeader());
    }
  }

  /**
   * Provides data for testing aside element.
   *
   * @return array
   *   An array with data.
   */
  public function asideHtmlProvider(): array {
    $data = [];

    $data['empty'] = [
      'Hello, world!',
      NULL,
      NULL,
      NULL,
    ];

    $data['simple'] = [
      <<<'HTML'
      <aside data-type="note">
        Hello, world!
      </aside>
      HTML,
      ContentAsideElement::class,
      'note',
      NULL,
    ];

    $data['with header'] = [
      <<<'HTML'
      <aside data-type="warning" data-header="Warning!">
        Hello, world!
      </aside>
      HTML,
      ContentAsideElement::class,
      'warning',
      'Warning!',
    ];

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->contentHtmlParser = $this->container->get('druki_content.parser.content_html_parser');
    $this->asideElementParser = $this->container->get('druki_content.parser.content_html_aside_element');
  }

}
