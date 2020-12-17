<?php

namespace Druki\Tests\Functional\Markdown;

use Drupal\druki\Markdown\Parser\MarkdownParser;
use Drupal\Tests\UnitTestCase;

/**
 * Provides tests for custom front matter markdown syntax.
 *
 * @covers \Drupal\druki\Markdown\CommonMark\Block\Element\FrontMatterElement
 * @covers \Drupal\druki\Markdown\CommonMark\Block\Parser\FrontMatterParser
 * @covers \Drupal\druki\Markdown\CommonMark\Block\Renderer\FrontMatterRenderer
 */
final class FrontMatterSyntaxTest extends UnitTestCase {

  /**
   * The markdown parser.
   *
   * @var \Drupal\druki\Markdown\Parser\MarkdownParser
   */
  protected $markdown;

  /**
   * Tests behavior when provided syntax is valid.
   */
  public function testValid(): void {
    $front_matter = <<<'Markdown'
---
id: test
core: 8
metatags:
  title: Overridden metatag title.
---
Markdown;

    $result = $this->markdown->parse($front_matter);
    $expected_result = <<<'HTML'
<div data-druki-element="front-matter">{"id":"test","core":8,"metatags":{"title":"Overridden metatag title."}}</div>

HTML;
    $this->assertEquals($expected_result, $result);
  }

  /**
   * Tests when FrontMatter syntax no on the first line.
   */
  public function testNotAtTheStart(): void {
    $front_matter = <<<'Markdown'

---
id: test
core: 8
metatags:
  title: Overridden metatag title.
---
Markdown;

    $result = $this->markdown->parse($front_matter);
    $this->assertStringNotContainsString('data-druki-element="front-matter"', $result);
  }

  /**
   * Tests when FrontMatter tried to be used with more than 3 dashes.
   */
  public function testWrongDashCount(): void {
    $front_matter = <<<'Markdown'
----
id: test
core: 8
metatags:
  title: Overridden metatag title.
----
Markdown;

    $result = $this->markdown->parse($front_matter);
    $this->assertStringNotContainsString('data-druki-element="front-matter"', $result);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->markdown = new MarkdownParser();
  }

}
