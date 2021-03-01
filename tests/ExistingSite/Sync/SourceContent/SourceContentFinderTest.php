<?php

namespace Druki\Tests\ExistingSite\Sync\SourceContent;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_content\Sync\SourceContent\SourceContent;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for source content finder.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\SourceContent\SourceContentFinder
 */
final class SourceContentFinderTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * Tests ability to find content.
   */
  public function testFindAll(): void {
    $directory = $this->setupFakeSourceDir();
    /** @var \Drupal\druki_content\Sync\SourceContent\SourceContentFinder $content_finder */
    $content_finder = $this->container->get('druki_content.source_content_finder');
    $content_list = $content_finder->findAll($directory->url());
    // The current site has only Russian language enabled. So it's expected that
    // finder will skip all unsupported language content.
    $this->assertEquals(2, $content_list->numberOfItems());
    $content_list_array = $content_list->toArray();
    $first_content = \array_shift($content_list_array);
    $this->assertTrue($first_content instanceof SourceContent);
    $this->assertEquals('ru', $first_content->getLanguage());
    $this->assertEquals('vfs://content/docs/ru/standards/php.md', $first_content->getRealpath());
    $this->assertEquals('docs/ru/standards/php.md', $first_content->getRelativePathname());
  }

}
