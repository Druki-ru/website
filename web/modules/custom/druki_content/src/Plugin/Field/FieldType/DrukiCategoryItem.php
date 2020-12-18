<?php

namespace Drupal\druki_content\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'druki_category' field type.
 *
 * @FieldType(
 *   id = "druki_category",
 *   label = @Translation("Druki content category"),
 *   category = @Translation("Druki"),
 *   no_ui = TRUE,
 * )
 */
class DrukiCategoryItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {

    $properties['area'] = DataDefinition::create('string')
      ->setLabel(\t('Area'))
      ->setRequired(TRUE);

    $properties['order'] = DataDefinition::create('integer')
      ->setLabel(\t('Order'))
      ->setRequired(TRUE);

    $properties['title'] = DataDefinition::create('string')
      ->setLabel(\t('Area'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {

    $columns = [
      'area' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'Category area.',
        'length' => 255,
      ],
      'order' => [
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
      ],
      'title' => [
        'type' => 'varchar',
        'description' => 'Category area.',
        'length' => 255,
      ],
    ];

    $schema = [
      'columns' => $columns,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    $random = new Random();
    $values['area'] = $random->word(\mt_rand(1, 50));
    $values['order'] = \rand(0, 10);
    $values['title'] = $random->word(\mt_rand(1, 50));

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName(): string {
    return 'area';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $value = $this->get('area')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * Gets category area name.
   *
   * @return string
   *   The category area.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getCategoryArea(): string {
    return $this->get('area')->getValue();
  }

  /**
   * Gets item order inside category.
   *
   * @return int
   *   The order of item.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getCategoryItemOrder(): int {
    return $this->get('order')->getValue();
  }

  /**
   * Gets item title for category listing.
   *
   * @return string
   *   The item title.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getCategoryItemTitle(): string {
    return $this->get('title')->getValue() ?? $this->getEntity()->label();
  }

}
