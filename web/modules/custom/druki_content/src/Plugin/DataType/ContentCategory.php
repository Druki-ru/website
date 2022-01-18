<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * Provides data type representing content category.
 *
 * @DataType(
 *   id = "druki_content_category",
 *   label = @Translation("Content category"),
 *   definition_class = "\Drupal\druki_content\TypedData\ContentCategoryDefinition"
 * )
 */
final class ContentCategory extends Map {

  /**
   * Gets category area.
   *
   * @return string
   *   A category area.
   */
  public function getArea(): string {
    return $this->get('area')->getString();
  }

  /**
   * Gets category order.
   *
   * @return int|null
   *   A category order.
   */
  public function getOrder(): ?int {
    return $this->get('order')->getValue();
  }

  /**
   * Gets content title in a category.
   *
   * @return string
   *   A content title.
   */
  public function getTitle(): ?string {
    return $this->get('title')->getValue();
  }

}
