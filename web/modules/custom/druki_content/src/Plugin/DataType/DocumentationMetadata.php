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
 *
 * @see \Drupal\druki_content\TypedData\DocumentationMetadataDefinition
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
    if (!$this->hasCore()) {
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
   * Gets category information.
   *
   * @return array|null
   *   An array with category information. Contains:
   *   - area: (required) A category group name.
   *   - order: An integer with order of current content in the provided area.
   *   - title: A content title override for category navigation block.
   */
  public function getCategory(): ?array {
    if (!$this->hasCategory()) {
      return NULL;
    }
    $category = $this->get('category')->getValue();
    if (\is_null($category['order'])) {
      $category['order'] = 0;
    }
    return $category;
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

  /**
   * Gets content metatags.
   *
   * @return array|null
   *   An array with metatags.
   */
  public function getMetatags(): ?array {
    if (!$this->hasMetatags()) {
      return NULL;
    }

    $metatags = [];
    if ($this->hasMetatag('title')) {
      $metatags['title'] = $this->getMetatag('title');
      $metatags['og_title'] = $this->getMetatag('title');
      $metatags['twitter_cards_title'] = $this->getMetatag('title');
    }
    if ($this->hasMetatag('description')) {
      $metatags['description'] = $this->getMetatag('description');
      $metatags['og_description'] = $this->getMetatag('description');
    }

    return $metatags;
  }

  /**
   * Checks is metatags are set.
   *
   * @return bool
   *   TRUE if metatags are set, FALSE otherwise.
   */
  public function hasMetatags(): bool {
    return !empty($this->get('metatags')->getValue());
  }

  /**
   * Checks is metatag with a specific name is set.
   *
   * @param string $name
   *   A metatag name.
   *
   * @return bool
   *   TRUE if value for request metatag is set.
   */
  public function hasMetatag(string $name): bool {
    return isset($this->get('metatags')->getValue()[$name]);
  }

  /**
   * Gets a value for specific metatag.
   *
   * @param string $name
   *   A metatag name.
   *
   * @return string|null
   *   A metatag value.
   */
  public function getMetatag(string $name): ?string {
    if (!$this->hasMetatag($name)) {
      return NULL;
    }
    return $this->get('metatags')->getValue()[$name];
  }

  /**
   * Gets search keywords.
   *
   * @return array|null
   *   An array with search keywords.
   */
  public function getSearchKeywords(): ?array {
    if (!$this->hasSearchKeywords()) {
      return NULL;
    }
    return $this->get('search-keywords')->getValue();
  }

  /**
   * Checks is search keywords are set.
   *
   * @return bool
   *   TRUE if search keywords are provided.
   */
  public function hasSearchKeywords(): bool {
    return !empty($this->get('search-keywords')->getValue());
  }

  /**
   * Gets authors of a content.
   *
   * @return array|null
   *   An array with author IDs.
   */
  public function getAuthors(): ?array {
    if (!$this->hasAuthors()) {
      return NULL;
    }
    return $this->get('authors')->getValue();
  }

  /**
   * Checks is authors are set.
   *
   * @return bool
   *   TRUE if authors are set.
   */
  public function hasAuthors(): bool {
    return !empty($this->get('authors')->getValue());
  }

  /**
   * Gets checksum for current documentation metadata values.
   *
   * @return string
   *   A checksum for instance.
   */
  public function checksum(): string {
    $checksum_parts = $this->toArray();
    return \md5(\serialize($checksum_parts));
  }

}
