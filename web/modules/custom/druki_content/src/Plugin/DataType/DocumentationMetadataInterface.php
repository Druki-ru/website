<?php

namespace Drupal\druki_content\Plugin\DataType;

/**
 * Provides an interface for documentation metadata typed data.
 */
interface DocumentationMetadataInterface {

  /**
   * Gets content title.
   *
   * @return string
   *   A content title.
   */
  public function getTitle(): string;

  /**
   * Gets content slug.
   *
   * @return string
   *   A content slug.
   */
  public function getSlug(): string;

  /**
   * Gets content Drupal core.
   *
   * @return int|null
   *   A Drupal core version.
   */
  public function getCore(): ?int;

  /**
   * Checks for core value.
   *
   * @return bool
   *   TRUE if core is set.
   */
  public function hasCore(): bool;

  /**
   * Gets category information.
   *
   * @return array|null
   *   An array with category information. Contains:
   *   - area: (required) A category group name.
   *   - order: An integer with order of current content in the provided area.
   *   - title: A content title override for category navigation block.
   */
  public function getCategory(): ?array;

  /**
   * Checks is category values are set.
   *
   * @return bool
   *   TRUE if category value is set.
   */
  public function hasCategory(): bool;

  /**
   * Gets content metatags.
   *
   * @return array|null
   *   An array with metatags.
   */
  public function getMetatags(): ?array;

  /**
   * Checks is metatags are set.
   *
   * @return bool
   *   TRUE if metatags are set, FALSE otherwise.
   */
  public function hasMetatags(): bool;

  /**
   * Checks is metatag with a specific name is set.
   *
   * @param string $name
   *   A metatag name.
   *
   * @return bool
   *   TRUE if value for request metatag is set.
   */
  public function hasMetatag(string $name): bool;

  /**
   * Gets a value for specific metatag.
   *
   * @param string $name
   *   A metatag name.
   *
   * @return string|null
   *   A metatag value.
   */
  public function getMetatag(string $name): ?string;

  /**
   * Gets search keywords.
   *
   * @return array|null
   *   An array with search keywords.
   */
  public function getSearchKeywords(): ?array;

  /**
   * Checks is search keywords are set.
   *
   * @return bool
   *   TRUE if search keywords are provided.
   */
  public function hasSearchKeywords(): bool;

  /**
   * Gets authors of a content.
   *
   * @return array|null
   *   An array with author IDs.
   */
  public function getAuthors(): ?array;

  /**
   * Checks is authors are set.
   *
   * @return bool
   *   TRUE if authors are set.
   */
  public function hasAuthors(): bool;

  /**
   * Gets checksum for current documentation metadata values.
   *
   * @return string
   *   A checksum for instance.
   */
  public function checksum(): string;

}
