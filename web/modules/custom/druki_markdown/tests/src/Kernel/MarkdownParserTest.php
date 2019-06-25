<?php

namespace Drupal\Tests\druki_markdown\Kernel;

use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\token\Kernel\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Test markdown parser.
 */
class MarkdownParserTest extends KernelTestBase {

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
   * Tests markdown parser.
   */
  public function testMarkdownParser() {
    $this->doCommonMarkSyntaxParsingTest();
    //    $this->testCustomSyntaxParsing();
  }

  /**
   * Tests CommonMark syntax parsing.
   *
   * The CommonMark syntax is default one. We just need to ensure, that markdown
   * parser works at the minimum required syntax.
   *
   * @see https://commonmark.org/help/
   *   The default syntax cheatsheet.
   */
  protected function doCommonMarkSyntaxParsingTest() {
    $this->doItalicTest();
    $this->doBoldTest();
    $this->doTestHeading();
  }

  /**
   * Tests for italic.
   */
  protected function doItalicTest() {
    $actual = $this->markdownParser->parse('*italic*');
    $this->assertRegExp("/<p><em>italic<\/em><\/p>[\r\n]/", $actual);

    $actual = $this->markdownParser->parse('_italic_');
    $this->assertRegExp("/<p><em>italic<\/em><\/p>[\r\n]/", $actual);
  }

  /**
   * Tests for bold.
   */
  protected function doBoldTest() {
    $actual = $this->markdownParser->parse('**bold**');
    $this->assertRegExp("/<p><strong>bold<\/strong><\/p>[\r\n]/", $actual);

    $actual = $this->markdownParser->parse('__bold__');
    $this->assertRegExp("/<p><strong>bold<\/strong><\/p>[\r\n]/", $actual);
  }

  /**
   * Test for headings.
   *
   * @todo use dataProvider
   */
  protected function doTestHeading() {
    $actual = $this->markdownParser->parse('# Heading 1');
    $this->assertRegExp("/<h1>Heading 1<\/h1>[\r\n]/", $actual);

    $actual = $this->markdownParser->parse('## Heading 2');
    $this->assertRegExp("/<h2>Heading 2<\/h2>[\r\n]/", $actual);

    $actual = $this->markdownParser->parse('### Heading 3');
    $this->assertRegExp("/<h3>Heading 3<\/h3>[\r\n]/", $actual);

    $actual = $this->markdownParser->parse('#### Heading 4');
    $this->assertRegExp("/<h4>Heading 4<\/h4>[\r\n]/", $actual);

    $actual = $this->markdownParser->parse('##### Heading 5');
    $this->assertRegExp("/<h5>Heading 5<\/h5>[\r\n]/", $actual);

    $actual = $this->markdownParser->parse('###### Heading 6');
    $this->assertRegExp("/<h6>Heading 6<\/h6>[\r\n]/", $actual);

    $actual = $this->markdownParser->parse('####### Heading with more than 6 hashes');
    $this->assertRegExp("/<p>####### Heading with more than 6 hashes<\/p>[\r\n]/", $actual);
  }

  //  /**
  //   * Tests for custom markdown syntax .
  //   */
  //  public function testCustomSyntaxParsing() {
  //    $markdown_parser = $this->container->get('druki_markdown.parser');
  //
  //    $value = <<<Markdown
  //---
  //id: test
  //core: 8
  //metatags:
  //  title: This is the metatag overridden title.
  //---
  //Markdown;
  //    $actual = $markdown_parser->parse($value);
  //    echo $actual;
  //  }
  //
  //  /**
  //   * Tests for custom note syntax.
  //   */
  //  public function testNote() {
  //    $markdown_parser = $this->container->get('druki_markdown.parser');
  //    $value = <<<Markdown
  //> [!NOTE]
  //> Test for the note.
  //Markdown;
  //
  //    $actual = $markdown_parser->parse($value);
  //    echo $actual;
  //  }

}
