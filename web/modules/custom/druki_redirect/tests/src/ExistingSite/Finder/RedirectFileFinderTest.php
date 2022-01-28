<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_redirect\ExistingSite\Finder;

use org\bovigo\vfs\vfsStream;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for redirect file finder.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Finder\RedirectFileFinder
 */
final class RedirectFileFinderTest extends ExistingSiteBase {

  /**
   * Tests that finder found only expected files.
   */
  public function testFinder(): void {
    vfsStream::setup();
    vfsStream::create([
      'foo' => [
        'redirects.csv' => '',
      ],
      'docs' => [
        'ru' => [
          'bar' => [
            'redirects.csv' => '',
          ],
          'redirects.csv' => '',
        ],
        'redirects.csv' => '',
      ],
    ]);

    /** @var \Drupal\druki_redirect\Finder\RedirectFileFinder $finder */
    $finder = $this->container->get('druki_redirect.finder.redirect_file');

    $redirect_files = $finder->findAll([vfsStream::url('root/foo')]);
    $this->assertEmpty($redirect_files->getIterator());

    $redirect_files = $finder->findAll([vfsStream::url('root/docs')]);
    $this->assertCount(1, $redirect_files->getIterator());

    $this->expectException(\InvalidArgumentException::class);
    $finder->findAll([]);
  }

}
