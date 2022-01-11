<?php

declare(strict_types=1);

namespace Drupal\druki_author\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Provides formatter to display proper description as 'about' for an author.
 *
 * @FieldFormatter(
 *   id = "druki_author_description",
 *   label = @Translation("Description"),
 *   field_types = {
 *     "map",
 *   },
 * )
 */
final class DescriptionFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    $is_description_field = $field_definition->getName() == 'description';
    $is_author_entity = $field_definition->getTargetEntityTypeId() == 'druki_author';
    return $is_description_field && $is_author_entity;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    /** @var \Drupal\Core\Field\Plugin\Field\FieldType\MapItem $item */
    foreach ($items as $item) {
      $values = $item->getValue();
      $description_languages = \array_keys($values);
      if (!\in_array($langcode, $description_languages)) {
        continue;
      }
      $elements[] = [
        '#markup' => $values[$langcode],
      ];
    }
    return $elements;
  }

}
