<?php

namespace Drupal\druki\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\druki\Data\Contributor;

/**
 * Defines the 'druki_contributors' field type.
 *
 * @FieldType(
 *   id = "druki_contributor",
 *   label = @Translation("Contributor"),
 *   category = @Translation("Druki"),
 *   no_ui = TRUE,
 * )
 */
final class ContributorItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['name'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('The contributor name'))
      ->setRequired(TRUE);

    $properties['email'] = DataDefinition::create('email')
      ->setLabel(new TranslatableMarkup('The contributor email'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    $columns = [
      'name' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'The contributor name.',
        'length' => 255,
      ],
      'email' => [
        'type' => 'varchar',
        'not null' => TRUE,
        'description' => 'The contributor email.',
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
    return 'name';
  }

  /**
   * Gets contributor name.
   *
   * @return string
   *   The contributor name.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getContributorName(): string {
    return $this->get('name')->getValue();
  }

  /**
   * Gets contributor email.
   *
   * @return string
   *   The contributor email.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getContributorEmail(): string {
    return $this->get('email')->getValue();
  }

  /**
   * Gets value as Contributor value object.
   *
   * @return \Drupal\druki\Data\Contributor
   *   The value object with values.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function toContributor(): Contributor {
    return new Contributor($this->getContributorName(), $this->getContributorEmail());
  }

}
