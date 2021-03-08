<?php

namespace Druki\Tests\ExistingSite\Sync;

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
    /** @var \Drupal\druki_content\Sync\Redirect\RedirectFinder $finder */
    $finder = $this->container->get('druki_content.redirect.finder');
    $redirect_list = $finder->findAll($source_dir->url());
    $expected_content = '/foo-bar,/' . \PHP_EOL;
    $redirect_list->getIterator()->rewind();
    $first_redirect = $redirect_list->getIterator()->current();
    $this->assertEquals($expected_content, \file_get_contents($first_redirect->getPathname()));
    $expected_hash = \hash('sha256', $expected_content);
    $this->assertEquals($expected_hash, $first_redirect->getHash());
    $this->assertEquals('ru', $first_redirect->getLanguage());
  }

}
