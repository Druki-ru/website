<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

use Drupal\druki_content\Plugin\DataType\DocumentationMetadataInterface;

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
  protected DocumentationMetadataInterface $metadata;

  /**
   * The structured content.
   */
  protected Content $content;

  /**
   * Constructs a new ContentDocument object.
   *
   * @param string $language
   *   The content language.
   * @param \Drupal\druki_content\Plugin\DataType\DocumentationMetadataInterface $metadata
   *   The content metadata.
   * @param \Drupal\druki_content\Data\Content $content
   *   The structured content.
   */
  public function __construct(string $language, DocumentationMetadataInterface $metadata, Content $content) {
    $this->language = $language;
    $this->metadata = $metadata;
    $this->content = $content;
  }

  /**
   * Gets content metadata.
   *
   * @return \Drupal\druki_content\Plugin\DataType\DocumentationMetadataInterface
   *   The content metadata.
   */
  public function getMetadata(): DocumentationMetadataInterface {
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
