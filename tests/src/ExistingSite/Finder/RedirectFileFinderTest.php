<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Finder;

use Druki\Tests\Traits\SourceContentProviderTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for redirect file finder.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Finder\RedirectFileFinder
 */
final class RedirectFileFinderTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * Tests that finder found only expected files.
   */
  public function testFinder(): void {
    /** @var \Drupal\druki_redirect\Finder\RedirectFileFinder $finder */
    $finder = $this->container->get('druki_redirect.finder.redirect_file');
    $dir = $this->setupFakeSourceDir();

    $redirect_files = $finder->findAll([$dir->url()]);
    $this->assertEmpty($redirect_files->getIterator());

    $redirect_files = $finder->findAll([$dir->url() . '/docs']);
    $this->assertCount(1, $redirect_files->getIterator());
  }

}
