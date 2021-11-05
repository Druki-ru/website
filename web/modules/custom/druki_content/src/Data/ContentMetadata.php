<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides object that store metadata for contents.
 *
 * The metadata of content in source provided by Front Matter block in the
 * content source file.
 *
 * This object only holds values that are supported by a website all extra data
 * will be skipped.
 *
 * Website supports this metadata:
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
 *     related metatags <meta name="title">, <meta name='twitter:title'>,
 *     <meta property='og:title'>. This value does not change <h1> of the page.
 *   - description: (optional) Allows providing specific content description.
 *     This value will be used for <meta name='description'> and
 *     <meta property='og:description'>.
 */
final class ContentMetadata {

  /**
   * The content title.
   */
  protected string $title;

  /**
   * The content slug.
   */
  protected string $slug;

  /**
   * The Drupal core major version.
   */
  protected ?int $core = NULL;

  /**
   * An array with category metadata.
   */
  protected ?array $category = NULL;

  /**
   * An array with search keywords.
   */
  protected ?array $searchKeywords = NULL;

  /**
   * An array with metatags.
   */
  protected array $metatags;

  /**
   * Constructs a new ContentMetadata object from an array.
   *
   * @param array $data
   *   An associative array with metadata.
   *
   * @return static
   *   The new instance.
   */
  public static function createFromArray(array $data): self {
    $required_metadata = ['title', 'slug'];
    foreach ($required_metadata as $name) {
      if (!\in_array($name, \array_keys($data))) {
        $error = \sprintf('The required metadata values %s is missing.', $name);
        throw new \InvalidArgumentException($error);
      }
    }

    $instance = new self();
    $instance->title = (string) $data['title'];
    $instance->slug = (string) $data['slug'];

    if (isset($data['core'])) {
      $instance->core = \intval($data['core']);
    }

    if (isset($data['category'])) {
      if (!\is_array($data['category'])) {
        throw new \InvalidArgumentException("The 'category' metadata should be an array.");
      }
      if (!isset($data['category']['area'])) {
        throw new \InvalidArgumentException("The 'category.area' is required if 'category' is set.");
      }
      $default_values = [
        'order' => 0,
        'title' => NULL,
      ];
      $instance->category = $data['category'] + $default_values;
    }

    if (isset($data['search-keywords'])) {
      if (!\is_array($data)) {
        throw new \InvalidArgumentException("The 'search-keywords' metadata should be an array.");
      }
      $instance->searchKeywords = $data['search-keywords'];
    }

    if (isset($data['metatags'])) {
      if (!\is_array($data)) {
        throw new \InvalidArgumentException("The 'metatags' metadata should be an array.");
      }
      $allowed_metatags = ['title', 'description'];
      foreach ($allowed_metatags as $allowed_metatag) {
        if (isset($data['metatags'][$allowed_metatag])) {
          $instance->metatags[$allowed_metatag] = $data['metatags'][$allowed_metatag];
        }
      }
    }

    return $instance;
  }

  /**
   * Gets content title.
   *
   * @return string
   *   The title.
   */
  public function getTitle(): string {
    return $this->title;
  }

  /**
   * Gets content slug.
   *
   * @return string
   *   The slug.
   */
  public function getSlug(): string {
    return $this->slug;
  }

  /**
   * Gets core versions.
   *
   * @return int|null
   *   The core major number.
   */
  public function getCore(): ?int {
    return $this->core;
  }

  /**
   * Gets category metadata.
   *
   * @return array|null
   *   The category metadata contains:
   *   - area: The category group name.
   *   - order: The order in group.
   *   - title: The special title for content in group.
   */
  public function getCategory(): ?array {
    return $this->category;
  }

  /**
   * Checks metadata for search keywords value.
   *
   * @return bool
   *   TRUE if value is set.
   */
  public function hasSearchKeywords(): bool {
    return isset($this->searchKeywords);
  }

  /**
   * Gets search keywords.
   *
   * @return array|null
   *   An array with search keywords.
   */
  public function getSearchKeywords(): ?array {
    return $this->searchKeywords;
  }

  /**
   * Gets specific metatag value.
   *
   * @param string $name
   *   The metatag name.
   *
   * @return string|null
   *   The metatag value.
   */
  public function getMetatag(string $name): ?string {
    if (!$this->hasMetatag($name)) {
      return NULL;
    }
    return (string) $this->metatags[$name];
  }

  /**
   * Checks is specific metatag values is set.
   *
   * @param string $name
   *   The metatag name.
   *
   * @return bool
   *   TRUE if metatag is set.
   */
  public function hasMetatag(string $name): bool {
    return isset($this->metatags[$name]);
  }

  /**
   * Gets metatags array.
   *
   * @return array|null
   *   An array with metatags.
   */
  public function getMetatags(): ?array {
    if (!$this->hasMetatags()) {
      return NULL;
    }
    $metatags = $this->metatags;

    if ($this->hasMetatag('title')) {
      $metatags['og_title'] = $this->getMetatag('title');
      $metatags['twitter_cards_title'] = $this->getMetatag('title');
    }

    if ($this->hasMetatag('description')) {
      $metatags['og_description'] = $this->getMetatag('description');
    }

    return $metatags;
  }

  /**
   * Checks for metatags value.
   *
   * @return bool
   *   TRUE if at least single value for metatag is presented.
   */
  public function hasMetatags(): bool {
    return !empty($this->metatags);
  }

}
