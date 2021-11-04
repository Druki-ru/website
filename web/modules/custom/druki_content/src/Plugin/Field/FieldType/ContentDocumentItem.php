<?php

namespace Drupal\druki_content\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\druki_content\Data\ContentMetadata;

/**
 * Defines the 'druki_content_document' field type.
 *
 * @FieldType(
 *   id = "druki_content_document",
 *   label = @Translation("Druki content document"),
 *   category = @Translation("Druki"),
 *   no_ui = TRUE,
 *   default_formatter = "druki_content_document_render_array",
 * )
 */
class ContentDocumentItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['document'] = DataDefinition::create('druki_content_document')
      ->setLabel(new TranslatableMarkup('Content document'))
      ->setRequired(TRUE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'document' => [
          'type' => 'text',
          'size' => 'big',
        ],
      ],
    ];
  }

  /**
   * Gets content document.
   *
   * @return \Drupal\druki_content\Data\ContentDocument|null
   *   The content document.
   */
  public function getContentDocument(): ?ContentDocument {
    if ($this->isEmpty()) {
      return NULL;
    }
    return $this->get('document')->getContentDocument();
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    return [
      'document' => new ContentDocument('ru', new ContentMetadata(), new Content()),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName(): string {
    return 'document';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $value = $this->get('document')->getValue();
    return $value === NULL || $value === '';
  }

}
