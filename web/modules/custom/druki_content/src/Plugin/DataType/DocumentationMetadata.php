<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * Provides data type representing documentation metadata.
 *
 * @DataType(
 *   id = "druki_content_documentation_metadata",
 *   label = @Translation("Documentation metadata"),
 *   definition_class = "\Drupal\druki_content\TypedData\DocumentationMetadataDefinition"
 * )
 */
final class DocumentationMetadata extends Map {

  /**
   * Gets content title.
   *
   * @return string
   *   A content title.
   */
  public function getTitle(): string {
    return $this->get('title')->getValue();
  }

  /**
   * Gets content slug.
   *
   * @return string
   *   A content slug.
   */
  public function getSlug(): string {
    return $this->get('slug')->getValue();
  }

  /**
   * Gets content Drupal core.
   *
   * @return int|null
   *   A Drupal core version.
   */
  public function getCore(): ?int {
    if (!$this->get('core')->getValue()) {
      return NULL;
    }
    return $this->get('core')->getCastedValue();
  }

  /**
   * Checks for core value.
   *
   * @return bool
   *   TRUE if core is set.
   */
  public function hasCore(): bool {
    return !\is_null($this->get('core')->getValue());
  }

  /**
   * Checks is category values are set.
   *
   * @return bool
   *   TRUE if category value is set.
   */
  public function hasCategory(): bool {
    return !empty($this->get('category')->getValue());
  }

}
