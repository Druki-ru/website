<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Finder;

use Drupal\druki_author\Data\AuthorsFile;
use Drupal\druki_author\Finder\AuthorsFileFinder;
use Drupal\Tests\druki_content\Traits\SourceContentProviderTrait;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for authors file finder.
 *
 * @coversDefaultClass \Drupal\druki_author\Finder\AuthorsFileFinder
 */
final class AuthorsFileFinderTest extends UnitTestCase {

  use SourceContentProviderTrait;

  /**
   * Tests finder that it finds file correctly.
   */
  public function testFinder(): void {
    $directory = $this->setupFakeSourceDir();
    $finder = new AuthorsFileFinder();

    $this->assertNull($finder->find('foo/bar'));

    $this->assertNull($finder->find($directory->url()));

    $result = $finder->find($directory->url() . '/authors');
    $this->assertInstanceOf(AuthorsFile::class, $result);
  }

}
