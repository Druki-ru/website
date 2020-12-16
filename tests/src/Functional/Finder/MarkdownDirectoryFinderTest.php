<?php

namespace Druki\Tests\Functional\Finder;

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
  public function testFinder(array $directories, int $expected): void {
    $this->setUpVfsStream();
    $discovery = new MarkdownDirectoryFinder($directories);
    $data = $discovery->findAll();

    $this->assertCount($expected, $data);
  }

  /**
   * Set up fake filesystem.
   */
  protected function setUpVfsStream(): void {
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
  }

  /**
   * Tests directories that doesn't exists.
   */
  public function testDirectoryNotFound(): void {
    $this->expectException('\Symfony\Component\Finder\Exception\DirectoryNotFoundException');
    $this->setUpVfsStream();
    $discovery = new MarkdownDirectoryFinder([vfsStream::url('content/docs/fr'), vfsStream::url('content/docs/es')]);
    $discovery->findAll();
  }

  /**
   * Provides sets of directories sets for testing.
   */
  public function directoriesProvider(): array {
    return [
      // The subdirectories.
      'multiple with subdirectories' => [
        [
          vfsStream::url('content/docs/ru'),
          vfsStream::url('content/docs/en'),
        ],
        4,
      ],
      // An empty directory.
      'empty directory' => [
        [vfsStream::url('content/docs/de')],
        0,
      ],
    ];
  }

}
