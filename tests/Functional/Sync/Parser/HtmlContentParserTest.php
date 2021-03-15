<?php

namespace Druki\Tests\Drupal\druki_content\Sync\Parser;

use Drupal\druki\Markdown\Parser\MarkdownParser;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphCode;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphHeading;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphImage;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphNote;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphText;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue;
use Drupal\druki_content\Sync\Parser\HtmlContentParser;
use PHPUnit\Framework\TestCase;

/**
 * Provides test for HTML content parser.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\Parser\HtmlContentParser
 */
class HtmlContentParserTest extends TestCase {

  /**
   * Provides test for parser.
   */
  public function testParse(): void {
    $markdown_content = \file_get_contents(__DIR__ . '/../../../fixtures/source-content.md');

    $markdown_parser = new MarkdownParser();
    $html = $markdown_parser->parse($markdown_content);

    $content_parser = new HtmlContentParser();
    $parsed_content = $content_parser->parse($html, 'foo/bar');

    $front_matter = $parsed_content->getFrontMatter();
    $expected_front_matter = [
      new FrontMatterValue('title', 'The title'),
      new FrontMatterValue('slug', 'example'),
    ];
    $this->assertEquals($expected_front_matter, $front_matter->getValues());

    $content_list = $parsed_content->getContent();

    $expected_code_value = <<<'HTML'
    <pre><code class="language-php">echo phpinfo();
    </code></pre>
    HTML;

    $expected_grouped_text_value = <<<'HTML'
    <p>Two consecutive text blocks.</p>
    <p>They must be grouped into single one.</p>
    HTML;

    $expected_content_values = [
      new ParagraphText('<p>Hello world!</p>'),
      new ParagraphHeading('h2', 'Heading'),
      new ParagraphText('<p>Content with internal <a href="foo/bar.md" data-druki-internal-link-filepath="foo/bar">link</a> and external <a href="https://example.com">link</a>.</p>'),
      new ParagraphCode($expected_code_value),
      new ParagraphNote('note', '<p>This is simple note.</p>'),
      new ParagraphImage('https://example.com/image.jpg', 'Image example'),
      // Test that consecutive texts concatenated in the single element.
      new ParagraphText($expected_grouped_text_value),
    ];
    foreach ($content_list->getIterator() as $key => $content_item) {
      $this->assertEquals($expected_content_values[$key], $content_item);
    }
  }

}
