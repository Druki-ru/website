<?php

namespace Druki\Tests\ExistingSite\Sync\SourceContent;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_content\Sync\SourceContent\SourceContent;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides source content value object test.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\SourceContent\SourceContent
 */
final class SourceContentTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * Tests main things that value object provides.
   */
  public function testValueObject(): void {
    $source_directory = $this->setupFakeSourceDir();
    $realpath = $source_directory->url() . '/docs/ru/drupal.md';
    $relative_pathname = 'docs/ru/drupal.md';
    $language = 'ru';

    $source_content = new SourceContent($realpath, $relative_pathname, $language);
    $this->assertTrue($source_content->isReadable());
    $this->assertEquals('Drupal description.', $source_content->getContent());
    $this->assertEquals($realpath, $source_content->getRealpath());
    $this->assertEquals($relative_pathname, $source_content->getRelativePathname());
    $this->assertEquals($language, $source_content->getLanguage());

    // Test correct serialization and deserialization. The SplFileInfo cannot be
    // serialized, so there is workaround to fix it.
    $serialized = \serialize($source_content);
    $unserialized_object = \unserialize($serialized);
    // This is enough to make sure file object is restored properly.
    $this->assertEquals('Drupal description.', $unserialized_object->getContent());
  }

}
