<?php

namespace Drupal\druki_toc\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'druki_tok' field type.
 *
 * @FieldType(
 *   id = "druki_toc",
 *   label = @Translation("TOC"),
 *   category = @Translation("General"),
 *   no_ui = TRUE,
 * )
 */
class TocItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['area'] = DataDefinition::create('string')
      ->setLabel(t('Area'))
      ->setRequired(TRUE);

    $properties['order'] = DataDefinition::create('integer')
      ->setLabel(t('Order'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [
      'area' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'TOC area.',
        'length' => 255,
      ],
      'order' => [
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
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
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['area'] = $random->word(mt_rand(1, 50));
    $values['order'] = rand(0, 10);

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'area';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('area')->getValue();

    return $value === NULL || $value === '';
  }

}
