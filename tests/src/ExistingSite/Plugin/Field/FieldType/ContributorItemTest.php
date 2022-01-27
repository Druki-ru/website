<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\Field\FieldType;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\druki\Data\Contributor;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\druki\Trait\EntityCleanupTrait;
use Drupal\Tests\druki_content\Trait\DrukiContentCreationTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for 'druki_contributor' field type.
 *
 * @coversDefaultClass \Drupal\druki\Plugin\Field\FieldType\ContributorItem
 */
final class ContributorItemTest extends ExistingSiteBase {

  use EntityCleanupTrait;
  use DrukiContentCreationTrait;

  /**
   * The content storage.
   */
  protected ContentEntityStorageInterface $contentStorage;

  /**
   * The field name used to install field.
   */
  protected string $fieldName;

  /**
   * @inheritDoc
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Tests that field works as expected.
   */
  public function testField(): void {
    $content = $this->createDrukiContent(['type' => 'documentation']);
    /** @var \Drupal\Core\Field\FieldItemListInterface $field */
    $field = $content->get($this->fieldName);
    $this->assertTrue($field->isEmpty());

    $field->appendItem([
      'name' => 'John Doe',
      'email' => 'john.doe@example.com',
    ]);

    $this->assertFalse($field->isEmpty());
    $this->assertEquals('John Doe', $field->first()->getContributorName());
    $this->assertEquals('john.doe@example.com', $field->first()->getContributorEmail());
    $this->assertEquals(1, $field->count());

    $contributor = $field->first()->toContributor();
    $this->assertInstanceOf(Contributor::class, $contributor);
    $this->assertEquals('John Doe', $contributor->getUsername());
    $this->assertEquals('john.doe@example.com', $contributor->getEmail());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storeEntityIds(['field_storage_config', 'field_config', 'druki_content']);

    $this->fieldName = 'field_druki_contributor';
    FieldStorageConfig::create([
      'entity_type' => 'druki_content',
      'field_name' => $this->fieldName,
      'type' => 'druki_contributor',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'druki_content',
      'bundle' => 'documentation',
      'field_name' => $this->fieldName,
    ])->save();

    $this->contentStorage = $this->container->get('entity_type.manager')
      ->getStorage('druki_content');
  }

}
