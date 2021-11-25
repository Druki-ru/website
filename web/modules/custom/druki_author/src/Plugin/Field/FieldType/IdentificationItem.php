<?php

namespace Drupal\druki_author\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'druki_identification' field type.
 *
 * @FieldType(
 *   id = "druki_identification",
 *   label = @Translation("Identification"),
 *   category = @Translation("Druki"),
 *   no_ui = TRUE,
 * )
 */
final class IdentificationItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['type'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Area'))
      ->setRequired(TRUE);

    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Order'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    $columns = [
      'type' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'The identification type.',
        'length' => 255,
      ],
      'value' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'The identification value.',
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
  public function isEmpty(): bool {
    $value = $this->get(self::mainPropertyName())->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName(): ?string {
    return 'value';
  }

  /**
   * Gets identification type.
   *
   * @return string
   *   The identification type.
   */
  public function getIdentificationType(): string {
    return $this->get('type')->getValue();
  }

  /**
   * Gets identification value.
   *
   * @return string
   *   The identification value.
   */
  public function getIdentificationValue(): string {
    return $this->get('value')->getValue();
  }

}
