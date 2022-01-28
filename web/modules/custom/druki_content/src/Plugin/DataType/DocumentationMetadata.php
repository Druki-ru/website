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
 *   definition_class =
 *   "\Drupal\druki_content\TypedData\DocumentationMetadataDefinition"
 * )
 *
 * @see \Drupal\druki_content\TypedData\DocumentationMetadataDefinition
 */
final class DocumentationMetadata extends Map implements DocumentationMetadataInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle(): string {
    return $this->get('title')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function getSlug(): string {
    return $this->get('slug')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function getCore(): ?int {
    if (!$this->hasCore()) {
      return NULL;
    }
    return $this->get('core')->getCastedValue();
  }

  /**
   * {@inheritdoc}
   */
  public function hasCore(): bool {
    return !\is_null($this->get('core')->getValue());
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function hasCategory(): bool {
    return !empty($this->get('category')->getValue());
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function hasMetatags(): bool {
    return !empty($this->get('metatags')->getValue());
  }

  /**
   * {@inheritdoc}
   */
  public function hasMetatag(string $name): bool {
    return isset($this->get('metatags')->getValue()[$name]);
  }

  /**
   * {@inheritdoc}
   */
  public function getMetatag(string $name): ?string {
    if (!$this->hasMetatag($name)) {
      return NULL;
    }
    return $this->get('metatags')->getValue()[$name];
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchKeywords(): ?array {
    if (!$this->hasSearchKeywords()) {
      return NULL;
    }
    return $this->get('search-keywords')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function hasSearchKeywords(): bool {
    return !empty($this->get('search-keywords')->getValue());
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthors(): ?array {
    if (!$this->hasAuthors()) {
      return NULL;
    }
    return $this->get('authors')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function hasAuthors(): bool {
    return !empty($this->get('authors')->getValue());
  }

  /**
   * {@inheritdoc}
   */
  public function checksum(): string {
    $checksum_parts = $this->toArray();
    return \md5(\serialize($checksum_parts));
  }

}
