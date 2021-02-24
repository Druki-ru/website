<?php

namespace Druki\Tests\Functional\Finder;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki\Finder\MarkdownDirectoryFinder;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * MarkdownDirectoryFinder unit tests.
 *
 * @coversDefaultClass \Drupal\druki\Finder\MarkdownDirectoryFinder
 */
class MarkdownDirectoryFinderTest extends UnitTestCase {

  use SourceContentProviderTrait;
  
  /**
   * Tests Markdown directory finder.
   *
   * @covers ::findAll
   * @dataProvider directoriesProvider
   */
  public function testFinder(array $directories, int $expected): void {
    $this->setupFakeSourceDir();
    $discovery = new MarkdownDirectoryFinder($directories);
    $data = $discovery->findAll();

    $this->assertCount($expected, $data);
  }

  /**
   * Tests directories that doesn't exists.
   */
  public function testDirectoryNotFound(): void {
    $this->expectException('\Symfony\Component\Finder\Exception\DirectoryNotFoundException');
    $this->setupFakeSourceDir();
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
