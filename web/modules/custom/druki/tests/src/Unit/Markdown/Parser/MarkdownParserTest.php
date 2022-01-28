<?php

namespace Drupal\Tests\druki\Markdown\Parser;

use Drupal\druki\Markdown\Parser\MarkdownParser;
use Drupal\Tests\UnitTestCase;

/**
 * Test markdown parser service.
 *
 * @coversDefaultClass \Drupal\druki\Markdown\Parser\MarkdownParser
 *
 * @todo This is not a Unit test, this is Integration test. Refactor to Kernel
 *   test.
 */
final class MarkdownParserTest extends UnitTestCase {

  /**
   * The markdown parser.
   */
  protected MarkdownParser $markdown;

  /**
   * Test default CommonMark syntax parsing.
   *
   * @covers ::parse
   * @dataProvider commonMarkProvider
   */
  public function testCommonMark(string $markdown, string $expected_regex): void {
    $result = $this->markdown->parse($markdown);
    $this->assertMatchesRegularExpression($expected_regex, $result);
  }

  /**
   * Provides tests for CommonMark syntax.
   *
   * @return array
   *   An array with markdown content.
   */
  public function commonMarkProvider(): array {
    $ul_list_markdown = <<<'Markdown'
    * List
    * List
    * List
    Markdown;

    $ol_list_markdown = <<<'Markdown'
    1. List
    2. List
    3. List
    Markdown;

    $code_block = <<<'Markdown'
    ```php
    echo 'Hello world';
    ```
    Markdown;

    return [
      'italic with asteriks' => [
        '*italic*',
        "/<p><em>italic<\/em><\/p>[\r\n]/",
      ],
      'italic with underline' => [
        '_italic_',
        "/<p><em>italic<\/em><\/p>[\r\n]/",
      ],
      'bold with asteriks' => [
        '**bold**',
        "/<p><strong>bold<\/strong><\/p>[\r\n]/",
      ],
      'bold with underline' => [
        '__bold__',
        "/<p><strong>bold<\/strong><\/p>[\r\n]/",
      ],
      'heading 1' => ['# Heading 1', "/<h1>Heading 1<\/h1>[\r\n]/"],
      'heading 2' => ['## Heading 2', "/<h2>Heading 2<\/h2>[\r\n]/"],
      'heading 3' => ['### Heading 3', "/<h3>Heading 3<\/h3>[\r\n]/"],
      'heading 4' => ['#### Heading 4', "/<h4>Heading 4<\/h4>[\r\n]/"],
      'heading 5' => ['##### Heading 5', "/<h5>Heading 5<\/h5>[\r\n]/"],
      'heading 6' => ['###### Heading 6', "/<h6>Heading 6<\/h6>[\r\n]/"],
      'heading 6+' => [
        '####### Heading with more than 6 hashes',
        "/<p>####### Heading with more than 6 hashes<\/p>[\r\n]/",
      ],
      'link' => [
        '[Link](http://drupal.org) ',
        "/<p><a href=\"http:\/\/drupal\.org\">Link<\/a><\/p>[\r\n]/",
      ],
      'image' => [
        '![Drupal](https://drupal.org/files/cta/graphic/drupal.svg)',
        "/<p><img src=\"https:\/\/drupal\.org\/files\/cta\/graphic\/drupal.svg\" alt=\"Drupal\" \/><\/p>[\r\n]/",
      ],
      'quote' => [
        '> quote',
        "/<blockquote>[\r\n]<p>quote<\/p>[\r\n]<\/blockquote>/",
      ],
      'unordered list' => [
        $ul_list_markdown,
        "/<ul>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<\/ul>[\r\n]/",
      ],
      'ordered list' => [
        $ol_list_markdown,
        "/<ol>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<\/ol>[\r\n]/",
      ],
      'horizontal line' => [' ---', "/<hr \/>[\r\n]/"],
      'dinkus' => ['***', "/<hr \/>[\r\n]/"],
      'inline code' => [
        '`Inline code` test.',
        "/<p><code>Inline code<\/code> test.<\/p>[\r\n]/",
      ],
      'code block' => [
        $code_block,
        "/<pre><code class=\"language-php\">echo 'Hello world';[\r\n]<\/code><\/pre>[\r\n]/",
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->markdown = new MarkdownParser();
  }

}
