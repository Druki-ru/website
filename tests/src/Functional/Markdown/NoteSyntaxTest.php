<?php

namespace Druki\Tests\Functional\Markdown;

use Drupal\druki\Markdown\Parser\MarkdownParser;
use Drupal\Tests\UnitTestCase;

/**
 * Provides tests for custom note markdown syntax.
 *
 * @covers \Drupal\druki\Markdown\CommonMark\Block\Element\NoteElement
 * @covers \Drupal\druki\Markdown\CommonMark\Block\Parser\NoteParser
 * @covers \Drupal\druki\Markdown\CommonMark\Block\Renderer\NoteRenderer
 */
final class NoteSyntaxTest extends UnitTestCase {

  /**
   * The markdown parser.
   *
   * @var \Drupal\druki\Markdown\Parser\MarkdownParser
   */
  protected $markdown;

  /**
   * Tests behavior when provided syntax is valid.
   *
   * @dataProvider allowedNoteProvider
   */
  public function testValid(string $markdown, string $expected): void {
    $result = $this->markdown->parse($markdown);
    $this->assertEquals($expected, $result);
  }

  /**
   * Tests that note content can use markdown as well.
   */
  public function testInnerMarkdown(): void {
    $markdown = <<<'Markdown'
> [!NOTE]
> This is the note with **bold** text.
Markdown;
    $expected_result = <<<'HTML'
<div data-druki-note="note"><p>This is the note with <strong>bold</strong> text.</p></div>

HTML;

    $result = $this->markdown->parse($markdown);
    $this->assertEquals($expected_result, $result);
  }

  /**
   * Tests that only supported note types can be used.
   */
  public function testInvalidType(): void {
    $markdown = <<<'Markdown'
> [!NOT_EXISTING]
> This is the note with **bold** text.
Markdown;

    $result = $this->markdown->parse($markdown);
    $this->assertStringNotContainsString('data-druki-note', $result);
  }

  /**
   * Tests that lowercase type is not supported even for valid types.
   */
  public function testLowerCaseType(): void {
    $markdown = <<<'Markdown'
> [!note]
> This is the note with **bold** text.
Markdown;

    $result = $this->markdown->parse($markdown);
    $this->assertStringNotContainsString('data-druki-note', $result);
  }

  /**
   * Provides valid note syntax.
   *
   * @return array
   */
  public function allowedNoteProvider(): array {
    $notes = [];

    $notes['note'][] = <<<'Markdown'
> [!NOTE]
> This is the simple note.
Markdown;
    $notes['note'][] = <<<'HTML'
<div data-druki-note="note"><p>This is the simple note.</p></div>

HTML;

    $notes['warning'][] = <<<'Markdown'
> [!WARNING]
> This is the warning note.
Markdown;
    $notes['warning'][] = <<<'HTML'
<div data-druki-note="warning"><p>This is the warning note.</p></div>

HTML;

    $notes['tip'][] = <<<'Markdown'
> [!TIP]
> This is the tip note.
Markdown;
    $notes['tip'][] = <<<'HTML'
<div data-druki-note="tip"><p>This is the tip note.</p></div>

HTML;

    $notes['important'][] = <<<'Markdown'
> [!IMPORTANT]
> This is the important note.
Markdown;
    $notes['important'][] = <<<'HTML'
<div data-druki-note="important"><p>This is the important note.</p></div>

HTML;

    return $notes;
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->markdown = new MarkdownParser();
  }

}
