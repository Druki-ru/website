<?php

namespace Drupal\Tests\druki\Finder;

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
   */
  public function testFinder(): void {
    vfsStream::setup();
    vfsStream::create([
      'foo' => [
        'index.md' => '',
      ],
      'bar' => [
        'index.md' => '',
        'baz' => [
          'index.md' => '',
          'wrong-name.md' => '',
        ],
      ],
    ]);

    $discovery = new MarkdownDirectoryFinder([vfsStream::url('root')]);
    $data = $discovery->findAll();
    $this->assertCount(3, $data);
  }

}
