<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Kernel\Markdown\CommonMark\Block;

use Drupal\druki\Markdown\Parser\MarkdownParserInterface;
use Drupal\Tests\druki\Kernel\DrukiKernelTestBase;

/**
 * Provides a test for <Aside> element testing.
 */
final class AsideBlockTest extends DrukiKernelTestBase {

  /**
   * A markdown parser.
   */
  protected ?MarkdownParserInterface $markdownParser;

  /**
   * Tests that elements parsed and renderer as expected.
   *
   * @param string $markdown
   *   A markdown contents.
   * @param string $html
   *   An expected HTML result.
   *
   * @dataProvider markdownProvider
   */
  public function testElement(string $markdown, string $html): void {
    $result_html = $this->markdownParser->parse($markdown);
    $result_html = \rtrim($result_html);
    $this->assertEquals($html, $result_html);
  }

  /**
   * Provides markdown for testing.
   *
   * @return array
   *   An array with testing data.
   */
  public function markdownProvider(): array {
    $data = [];

    $data['inline'] = [
      '<Aside></Aside>',
      '<Aside></Aside>',
    ];

    $data['indented 2'] = [
      <<<'Markdown'
        <Aside>
        Hello, World!
        </Aside>
      Markdown,
      <<<'HTML'
        <Aside>
        Hello, World!
        </Aside>
      HTML,
    ];

    $data['indented 4'] = [
      <<<'Markdown'
          <Aside>
          Hello, World!
          </Aside>
      Markdown,
      <<<'HTML'
      <pre><code>&lt;Aside&gt;
      Hello, World!
      &lt;/Aside&gt;
      </code></pre>
      HTML,
    ];

    $data['default type'] = [
      <<<'Markdown'
      <Aside>
      Hello, World!
      </Aside>
      Markdown,
      '<aside role="note" data-type="note"><p>Hello, World!</p></aside>',
    ];

    $data['note type'] = [
      <<<'Markdown'
      <Aside type="note">
      Hello, World!
      </Aside>
      Markdown,
      '<aside role="note" data-type="note"><p>Hello, World!</p></aside>',
    ];

    $data['warning type'] = [
      <<<'Markdown'
      <Aside type="warning">
      Hello, World!
      </Aside>
      Markdown,
      '<aside role="note" data-type="warning"><p>Hello, World!</p></aside>',
    ];

    $data['important type'] = [
      <<<'Markdown'
      <Aside type="important">
      Hello, World!
      </Aside>
      Markdown,
      '<aside role="note" data-type="important"><p>Hello, World!</p></aside>',
    ];

    $data['deprecated type'] = [
      <<<'Markdown'
      <Aside type="deprecated">
      Hello, World!
      </Aside>
      Markdown,
      '<aside role="note" data-type="deprecated"><p>Hello, World!</p></aside>',
    ];

    $data['invalid type'] = [
      <<<'Markdown'
      <Aside>
      Hello, World!
      </Aside>
      Markdown,
      '<aside role="note" data-type="note"><p>Hello, World!</p></aside>',
    ];

    $data['markdown inside'] = [
      <<<'Markdown'
      <Aside>
      **Hello, World!**
      </Aside>
      Markdown,
      '<aside role="note" data-type="note"><p><strong>Hello, World!</strong></p></aside>',
    ];

    $data['header'] = [
      <<<'Markdown'
      <Aside type="important" header="Foo bar!">
      **Hello, World!**
      </Aside>
      Markdown,
      '<aside role="note" data-type="important" data-header="Foo bar!"><p><strong>Hello, World!</strong></p></aside>',
    ];

    $data['complex'] = [
      <<<'Markdown'
      <Aside>
      **Hello, World!**
      </Aside>

      Hello, Robot!

      <Aside type="warning">
        Beep-boop-beep!
      </Aside>
      Markdown,
      <<<'HTML'
      <aside role="note" data-type="note"><p><strong>Hello, World!</strong></p></aside>
      <p>Hello, Robot!</p>
      <aside role="note" data-type="warning"><p>Beep-boop-beep!</p></aside>
      HTML,
    ];

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->markdownParser = $this->container->get('druki.markdown_parser');
  }

}
