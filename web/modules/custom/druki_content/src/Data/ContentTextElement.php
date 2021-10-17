<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides content text element.
 *
 * This block used for simple text.
 */
final class ContentTextElement extends ContentElementBase {

  /**
   * The block contents.
   */
  private string $content;

  /**
   * Constructs a new ContentTextElement object.
   *
   * @param string $content
   *   The contents.
   */
  public function __construct(string $content) {
    $this->content = $content;
  }

  /**
   * Gets block content.
   *
   * @return string
   *   The block content.
   */
  public function getContent(): string {
    return $this->content;
  }

}
