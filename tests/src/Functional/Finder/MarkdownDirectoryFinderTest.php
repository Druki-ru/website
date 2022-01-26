<?php

namespace Druki\Tests\Functional\Finder;

use Drupal\druki\Finder\MarkdownDirectoryFinder;
use Drupal\Tests\druki_content\Traits\SourceContentProviderTrait;
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
   */
  public function testFinder(): void {
    $root = $this->setupFakeSourceDir();
    $discovery = new MarkdownDirectoryFinder([$root->url()]);
    $data = $discovery->findAll();
    $this->assertCount(4, $data);
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

}
