<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Data;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_author\Data\AuthorsFile;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for author.json file object storage.
 */
final class AuthorsFileTest extends UnitTestCase {

  use SourceContentProviderTrait;

  /**
   * Tests that objects works as expected.
   */
  public function testObject(): void {
    $directory = $this->setupFakeSourceDir();

    $file_uri = $directory->url() . '/authors/authors.json';
    $authors_file = new AuthorsFile($file_uri);
    $this->assertEquals($file_uri, $authors_file->getPathname());
    $this->assertIsString($authors_file->getHash());

    $this->expectException(\InvalidArgumentException::class);
    new AuthorsFile('/some/random/uri/authors.json');
  }

}
