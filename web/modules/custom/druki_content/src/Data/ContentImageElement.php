<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides content image element.
 */
final class ContentImageElement extends ContentElementBase {

  /**
   * The image src.
   */
  protected string $src;

  /**
   * The image alternative text.
   */
  protected string $alt;

  /**
   * Constructs a new ContentImageElement object.
   *
   * @param string $src
   *   The image src.
   * @param string $alt
   *   The image alternative text.
   */
  public function __construct(string $src, string $alt) {
    $this->src = $src;
    $this->alt = $alt;
  }

  /**
   * Gets image src.
   *
   * @return string
   *   The image src.
   */
  public function getSrc(): string {
    return $this->src;
  }

  /**
   * Gets image alternative text.
   *
   * @return string
   *   The alternative text.
   */
  public function getAlt(): string {
    return $this->alt;
  }

}
