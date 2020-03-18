<?php

namespace Drupal\Tests\druki_markdown\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Test markdown parser service.
 *
 * @coversDefaultClass \Drupal\druki_markdown\Parser\MarkdownParser
 */
class MarkdownParserServiceTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['druki_markdown'];

  /**
   * The markdown parser service.
   *
   * @var \Drupal\druki_markdown\Parser\MarkdownParser|object
   */
  protected $markdownParser;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->markdownParser = $this->container->get('druki_markdown.parser');
  }

  /**
   * Tests markdown parsers doing its job.
   *
   * @param string $markdown_string
   *   The string with markdown markup.
   * @param string $expected_regexp
   *   The regexp pattern for expected results in HTML.
   *
   * @dataProvider provider
   *
   * @see https://commonmark.org/help/
   *   The default syntax cheatsheet.
   *
   * @covers ::parse
   */
  public function testMarkdown($markdown_string, $expected_regexp) {
    $actual = $this->markdownParser->parse($markdown_string);
    $this->assertRegExp($expected_regexp, $actual);
  }

  /**
   * Provides data for testing markdown syntax.
   *
   * @return array
   *   The array with data for testing.
   */
  public function provider() {
    $ul_list_markdown = <<<Markdown
* List
* List
* List
Markdown;

    $ol_list_markdown = <<<Markdown
1. List
2. List
3. List
Markdown;

    $code_block = <<<Markdown
```
echo 'Hello world';
```
Markdown;

    $front_matter = <<<Markdown
---
id: test
core: 8
metatags:
  title: Overridden metatag title.
---
Markdown;

    $note_block = <<<Markdown
> [!NOTE]
> The note text.
Markdown;

    return [
      // Standard markdown syntax.
      'italic with asteriks' => ['*italic*', "/<p><em>italic<\/em><\/p>[\r\n]/"],
      'italic with underline' => ['_italic_', "/<p><em>italic<\/em><\/p>[\r\n]/"],
      'bold with asteriks' => ['**bold**', "/<p><strong>bold<\/strong><\/p>[\r\n]/"],
      'bold with underline' => ['__bold__', "/<p><strong>bold<\/strong><\/p>[\r\n]/"],
      'heading 1' => ['# Heading 1', "/<h1>Heading 1<\/h1>[\r\n]/"],
      'heading 2' => ['## Heading 2', "/<h2>Heading 2<\/h2>[\r\n]/"],
      'heading 3' => ['### Heading 3', "/<h3>Heading 3<\/h3>[\r\n]/"],
      'heading 4' => ['#### Heading 4', "/<h4>Heading 4<\/h4>[\r\n]/"],
      'heading 5' => ['##### Heading 5', "/<h5>Heading 5<\/h5>[\r\n]/"],
      'heading 6' => ['###### Heading 6', "/<h6>Heading 6<\/h6>[\r\n]/"],
      'heading 6+' => ['####### Heading with more than 6 hashes', "/<p>####### Heading with more than 6 hashes<\/p>[\r\n]/"],
      'link' => ['[Link](http://drupal.org) ', "/<p><a href=\"http:\/\/drupal\.org\">Link<\/a><\/p>[\r\n]/"],
      'image' => ['![Drupal](https://drupal.org/files/cta/graphic/drupal.svg)', "/<p><img src=\"https:\/\/drupal\.org\/files\/cta\/graphic\/drupal.svg\" alt=\"Drupal\" \/><\/p>[\r\n]/"],
      'quote' => ['> quote', "/<blockquote>[\r\n]<p>quote<\/p>[\r\n]<\/blockquote>/"],
      'unordered list' => [$ul_list_markdown, "/<ul>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<\/ul>[\r\n]/"],
      'ordered list' => [$ol_list_markdown, "/<ol>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<\/ol>[\r\n]/"],
      'horizontal line' => [' ---', "/<hr \/>[\r\n]/"],
      'dinkus' => ['***', "/<hr \/>[\r\n]/"],
      'inline code' => ['`Inline code` test.', "/<p><code>Inline code<\/code> test.<\/p>[\r\n]/"],
      'code block' => [$code_block, "/<pre><code>echo 'Hello world';[\r\n]<\/code><\/pre>[\r\n]/"],
      // Custom markdown syntax and alterations.
      'front matter' => [$front_matter, "/<div data-druki-element=\"front-matter\">{\"id\":\"test\",\"core\":8,\"metatags\":{\"title\":\"Overridden metatag title.\"}}<\/div>[\r\n]/"],
      'note block' => [$note_block, "/<div data-druki-note=\"note\"><p>The note text.<\/p><\/div>[\r\n]/"],
    ];
  }

}
