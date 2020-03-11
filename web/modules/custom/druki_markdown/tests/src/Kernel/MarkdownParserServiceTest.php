<?php

namespace Drupal\Tests\druki_markdown\Kernel;

use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\token\Kernel\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Test markdown parser service.
 */
class MarkdownParserServiceTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['druki_markdown', 'markdown', 'user', 'filter'];

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

    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);
    $this->installConfig(['druki_markdown']);

    $filter_markdown = FilterFormat::load('markdown');

    // Create administrative user with UID 1. This ID hardcoded in
    // Drupal\druki_markdown\Parser:35.
    $this->createUser([$filter_markdown->getPermissionName()], 'admin', TRUE);

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
   *
   * @todo Improve it when learn more about testing.
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

    $meta_information = <<<Markdown
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
      ['*italic*', "/<p><em>italic<\/em><\/p>[\r\n]/"],
      ['_italic_', "/<p><em>italic<\/em><\/p>[\r\n]/"],
      ['**bold**', "/<p><strong>bold<\/strong><\/p>[\r\n]/"],
      ['__bold__', "/<p><strong>bold<\/strong><\/p>[\r\n]/"],
      ['# Heading 1', "/<h1>Heading 1<\/h1>[\r\n]/"],
      ['## Heading 2', "/<h2>Heading 2<\/h2>[\r\n]/"],
      ['### Heading 3', "/<h3>Heading 3<\/h3>[\r\n]/"],
      ['#### Heading 4', "/<h4>Heading 4<\/h4>[\r\n]/"],
      ['##### Heading 5', "/<h5>Heading 5<\/h5>[\r\n]/"],
      ['###### Heading 6', "/<h6>Heading 6<\/h6>[\r\n]/"],
      ['####### Heading with more than 6 hashes', "/<p>####### Heading with more than 6 hashes<\/p>[\r\n]/"],
      ['[Link](http://drupal.org) ', "/<p><a href=\"http:\/\/drupal\.org\">Link<\/a><\/p>[\r\n]/"],
      ['![Drupal](https://drupal.org/files/cta/graphic/drupal.svg)', "/<p><img src=\"https:\/\/drupal\.org\/files\/cta\/graphic\/drupal.svg\" alt=\"Drupal\" \/><\/p>[\r\n]/"],
      ['> quote', "/<blockquote>[\r\n]<p>quote<\/p>[\r\n]<\/blockquote>/"],
      [$ul_list_markdown, "/<ul>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<\/ul>[\r\n]/"],
      [$ol_list_markdown, "/<ol>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<li>List<\/li>[\r\n]<\/ol>[\r\n]/"],
      [' ---', "/<hr \/>[\r\n]/"],
      ['***', "/<hr \/>[\r\n]/"],
      ['`Inline code` test.', "/<p><code>Inline code<\/code> test.<\/p>[\r\n]/"],
      [$code_block, "/<pre><code>echo 'Hello world';[\r\n]<\/code><\/pre>[\r\n]/"],
      // Custom markdown syntax and alterations.
      [$meta_information, "/<div data-druki-element=\"front-matter\">{\"id\":\"test\",\"core\":8,\"metatags\":{\"title\":\"Overridden metatag title.\"}}<\/div>[\r\n]/"],
      [$note_block, "/<div data-druki-note=\"note\"><p>The note text.<\/p><\/div>[\r\n]/"],
    ];
  }

}
