<?php

namespace Drupal\Tests\druki\Unit\Finder;

use Drupal\druki\Finder\MarkdownDirectoryFinder;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * MarkdownDirectoryFinder unit tests.
 *
 * @coversDefaultClass \Drupal\druki\Finder\MarkdownDirectoryFinder
 */
class MarkdownDirectoryFinderTest extends UnitTestCase {

  /**
   * Tests Markdown directory finder.
   *
   * @covers ::findAll
   * @dataProvider directoriesProvider
   */
  public function testFinder(array $directories, array $expected) {
    vfsStream::setup('content', NULL, [
      'docs' => [
        'ru' => [
          'standards' => [
            'php.md' => 'Drupal PHP code standards.',
          ],
          'drupal.md' => 'Drupal description.',
        ],
        'en' => [
          'standards' => [
            'php.md' => 'Drupal PHP code standards.',
          ],
          'drupal.md' => 'Drupal description.',
        ],
        'de' => [],
      ],
      'README.md' => "Readme file.",
    ]);

    $discovery = new MarkdownDirectoryFinder($directories);
    $data = $discovery->findAll();

    $this->assertSame($expected, $data);
  }

  /**
   * Provides sets of directories sets for testing.
   */
  public function directoriesProvider() {
    return [
      // The subdirectories.
      'test_1' => [
        [
          vfsStream::url('content/docs/ru'),
          vfsStream::url('content/docs/en'),
        ],
        [
          'vfs://content/docs/ru/standards/php.md' => 'php.md',
          'vfs://content/docs/ru/drupal.md' => 'drupal.md',
          'vfs://content/docs/en/standards/php.md' => 'php.md',
          'vfs://content/docs/en/drupal.md' => 'drupal.md',
        ],
      ],
      // An empty directory.
      'test_2' => [
        [vfsStream::url('content/docs/de')],
        [],
      ],
      // Directories that do not exist.
      'test_3' => [
        [
          vfsStream::url('content/docs/fr'),
          vfsStream::url('content/docs/es'),
        ],
        [],
      ],
    ];
  }

}
