<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\Unit\Finder;

use Drupal\druki_author\Data\AuthorsFile;
use Drupal\druki_author\Finder\AuthorsFileFinder;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides test for authors file finder.
 *
 * @coversDefaultClass \Drupal\druki_author\Finder\AuthorsFileFinder
 */
final class AuthorsFileFinderTest extends UnitTestCase {

  /**
   * Tests finder that it finds file correctly.
   */
  public function testFinder(): void {
    vfsStream::setup();
    vfsStream::create([
      'not-a-folder' => '',
      'authors' => [
        'authors.json' => '',
      ],
    ]);

    $finder = new AuthorsFileFinder();

    $this->assertNull($finder->find(vfsStream::url('root/not-a-folder')));

    $this->assertNull($finder->find(vfsStream::url('root')));

    $result = $finder->find(vfsStream::url('root/authors'));
    $this->assertInstanceOf(AuthorsFile::class, $result);
  }

}
