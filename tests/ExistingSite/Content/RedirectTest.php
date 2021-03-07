<?php

namespace Druki\Tests\ExistingSite\Content;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Tests redirect functional.
 */
final class RedirectTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * Tests that our custom field is installed.
   */
  public function testFieldIsInstalled(): void {
    $definition_manager = $this->container->get('entity.definition_update_manager');
    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    $field_definition = $definition_manager->getFieldStorageDefinition('druki_content_redirect', 'redirect');
    $this->assertTrue($field_definition instanceof BaseFieldDefinition);
    $this->assertEquals('boolean', $field_definition->getType());
  }

  /**
   * Tests finder for redirects files.
   */
  public function testRedirectFinder(): void {
    $source_dir = $this->setupFakeSourceDir();
    /** @var \Drupal\druki_content\Redirect\RedirectFinder $finder */
    $finder = $this->container->get('druki_content.redirect.finder');
    /** @var \Symfony\Component\Finder\SplFileInfo $first_file */
    $first_file = $finder->findAll($source_dir->url());
    $this->assertEquals('/foo-bar,/' . PHP_EOL, $first_file->getContents());
  }

}
