<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Unit\Data;

use Drupal\druki_author\Data\AuthorsFile;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides test for author.json file object storage.
 *
 * @coversDefaultClass \Drupal\druki_author\Data\AuthorsFile
 */
final class AuthorsFileTest extends UnitTestCase {

  /**
   * Tests that objects works as expected.
   */
  public function testObject(): void {
    vfsStream::setup();
    vfsStream::create([
      'authors.json' => '',
    ]);

    $file_uri = vfsStream::url('root/authors.json');
    $authors_file = new AuthorsFile($file_uri);
    $this->assertEquals($file_uri, $authors_file->getPathname());
    $this->assertIsString($authors_file->getHash());

    $this->expectException(\InvalidArgumentException::class);
    new AuthorsFile('/some/random/uri/authors.json');
  }

}
