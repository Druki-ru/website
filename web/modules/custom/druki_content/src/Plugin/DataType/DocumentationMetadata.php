<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * Provides data type representing documentation metadata.
 *
 * Can contain this metadata:
 * - title: (required) The content title. This will be used in <h1> and <title>.
 * - slug: (required) The unique slug of the document. This will be used as URL
 *   suffix. E.g., if the current prefix is 'wiki/' and slug is 'drupal/about'
 *   that means that content will be available by
 *   https://example.com/wiki/drupal/about URL.
 *   The slug also used to find out previously created content for update
 *   instead of creating new one, this means, that value is also serves as ID
 *   and because of that should be unique across all content in single language.
 * - core: (optional) The Drupal core major version.
 * - category: (optional) The category allows grouping several contents into one
 *   group of content with navigation between them. The category definition is
 *   an array, which contains:
 *   - area: (required) The category area. The content with same category area
 *     set will be grouped.
 *   - order: (optional) The position of the current content in the group. By
 *     default all have order = 0. Sort is ascending — the lower order will be
 *     showed first.
 *   - title: (optional) The override for content title in the grouped list.
 * - search-keywords: (optional) An array with search keywords that can be used
 *   for search that content and not the part of the content or should be
 *   boosted is search rankings. E.g., content about Libraries API can contain
 *   such keywords: 'how to add javascript css', 'how to add script on the
 *   page'. These keywords have extra priority over content.
 * - metatags: (optional) An array with content metatags:
 *   - title: (optional) Allows overriding <title> value for the page as well as
 *     related metatags <meta name='title'>, <meta name='twitter:title'>,
 *     <meta property='og:title'>. This value does not change <h1> of the page.
 *   - description: (optional) Allows providing specific content description.
 *     This value will be used for <meta name='description'> and
 *     <meta property='og:description'>.
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
   * Checks is search keywords are set.
   *
   * @return bool
   *   TRUE if search keywords are provided.
   */
  public function hasSearchKeywords(): bool {
    return !empty($this->get('search-keywords')->getValue());
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

}
