<?php

namespace Drupal\Tests\druki_markdown\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;

/**
 * Test markdown parser.
 */
class MarkdownParserTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['druki_markdown', 'markdown', 'user', 'filter'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installConfig(['druki_markdown']);
  }

  /**
   * Tests CommonMark syntax parsing.
   *
   * The CommonMark syntax is default one. We just need to ensure, that markdown
   * parser works at the minimum required syntax.
   */
  public function testCommonMarkSyntaxParsing() {
    $markdown_parser = $this->container->get('druki_markdown.parser');
    $actual = $markdown_parser->parse('**bold**');

    $this->assertRegExp("/<p><strong>bold<\/strong><\/p>[\r\n]/", $actual);
  }

  /**
   * Tests for custom markdown syntax used for meta information.
   */
  public function testMetaInformation() {
    $markdown_parser = $this->container->get('druki_markdown.parser');
    $value = <<<Markdown
---
id: test
core: 8
metatags:
  title: This is the metatag overridden title.
---
Markdown;

    $actual = $markdown_parser->parse($value);
    echo $actual;
  }

  /**
   * Tests for custom note syntax.
   */
  public function testNote() {
    $markdown_parser = $this->container->get('druki_markdown.parser');
    $value = <<<Markdown
> [!NOTE]
> Test for the note.
Markdown;

    $actual = $markdown_parser->parse($value);
    echo $actual;
  }

}
