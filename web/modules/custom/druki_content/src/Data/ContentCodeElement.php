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
   * The code language.
   */
  protected ?string $language = NULL;

  /**
   * Constructs a new ContentCodeElement object.
   *
   * @param string $content
   *   The code content.
   * @param string|null $language
   *   The code language.
   */
  public function __construct(string $content, ?string $language = NULL) {
    $this->content = $content;
    $this->language = $language;
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

  /**
   * Gets code language.
   *
   * @return string|null
   *   The code language.
   */
  public function getLanguage(): ?string {
    return $this->language;
  }

}
