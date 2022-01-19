<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

use Drupal\druki_content\Plugin\DataType\DocumentationMetadata;

/**
 * Provides structured content document.
 */
final class ContentDocument {

  /**
   * The content language.
   */
  protected string $language;

  /**
   * The content metadata.
   */
  protected DocumentationMetadata $metadata;

  /**
   * The structured content.
   */
  protected Content $content;

  /**
   * Constructs a new ContentDocument object.
   *
   * @param string $language
   *   The content language.
   * @param \Drupal\druki_content\Plugin\DataType\DocumentationMetadata $metadata
   *   The content metadata.
   * @param \Drupal\druki_content\Data\Content $content
   *   The structured content.
   */
  public function __construct(string $language, DocumentationMetadata $metadata, Content $content) {
    $this->language = $language;
    $this->metadata = $metadata;
    $this->content = $content;
  }

  /**
   * Gets content metadata.
   *
   * @return \Drupal\druki_content\Plugin\DataType\DocumentationMetadata
   *   The content metadata.
   */
  public function getMetadata(): DocumentationMetadata {
    return $this->metadata;
  }

  /**
   * Gets structured content.
   *
   * @return \Drupal\druki_content\Data\Content
   *   The structured content.
   */
  public function getContent(): Content {
    return $this->content;
  }

  /**
   * Gets content language.
   *
   * @return string
   *   The content language.
   */
  public function getLanguage(): string {
    return $this->language;
  }

}
