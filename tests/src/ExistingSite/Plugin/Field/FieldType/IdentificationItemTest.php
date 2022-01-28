<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\Field\FieldType;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\druki\Traits\EntityCleanupTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for 'druki_identification' field type.
 *
 * @coversDefaultClass \Drupal\druki_author\Plugin\Field\FieldType\IdentificationItem
 */
final class IdentificationItemTest extends ExistingSiteBase {

  use EntityCleanupTrait;

  /**
   * The author storage.
   */
  protected ContentEntityStorageInterface $authorStorage;

  /**
   * The field name used to install field.
   */
  protected string $fieldName;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storeEntityIds([
      'field_storage_config',
      'field_config',
      'druki_author',
    ]);

    $this->fieldName = 'field_druki_identification';
    FieldStorageConfig::create([
      'entity_type' => 'druki_author',
      'field_name' => $this->fieldName,
      'type' => 'druki_identification',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'druki_author',
      'field_name' => $this->fieldName,
      'bundle' => 'druki_author',
    ])->save();

    $this->authorStorage = $this->container->get('entity_type.manager')
      ->getStorage('druki_author');
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Tests that field works as expected.
   */
  public function testField(): void {
    $author = $this->authorStorage->create();
    /** @var \Drupal\Core\Field\FieldItemListInterface $field */
    $field = $author->get($this->fieldName);
    $this->assertTrue($field->isEmpty());

    $field->appendItem([
      'type' => 'email',
      'value' => 'john.doe@example.com',
    ]);

    $this->assertFalse($field->isEmpty());
    $this->assertEquals('email', $field->first()->getIdentificationType());
    $this->assertEquals('john.doe@example.com', $field->first()->getIdentificationValue());
    $this->assertEquals(1, $field->count());
  }

}
