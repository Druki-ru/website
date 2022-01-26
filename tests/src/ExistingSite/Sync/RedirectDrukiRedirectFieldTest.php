<?php

namespace Druki\Tests\ExistingSite\Sync;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Tests\druki_content\Traits\SourceContentProviderTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test that redirect entity has 'druki_redirect' field.
 */
final class RedirectDrukiRedirectFieldTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * Tests that our custom field is installed.
   */
  public function testFieldIsInstalled(): void {
    $definition_manager = $this->container->get('entity.definition_update_manager');
    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    $field_definition = $definition_manager->getFieldStorageDefinition('druki_redirect', 'redirect');
    $this->assertTrue($field_definition instanceof BaseFieldDefinition);
    $this->assertEquals('boolean', $field_definition->getType());
  }

}
