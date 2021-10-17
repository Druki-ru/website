<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides content code element.
 */
final class ContentCodeElement extends ContentElementBase {

  /**
   * The code element content.
   */
  protected string $content;

  /**
   * Constructs a new ContentCodeElement object.
   *
   * @param string $content
   *   The code content.
   */
  public function __construct(string $content) {
    $this->content = $content;
  }

  /**
   * Gets element content.
   *
   * @return string
   *   The element content.
   */
  public function getContent(): string {
    return $this->content;
  }

}
