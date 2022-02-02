<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides an element tha represents '<Aside>' element.
 */
final class ContentAsideElement extends ContentElementBase {

  /**
   * A type of aside.
   */
  protected string $type;

  /**
   * A header for content.
   */
  protected ?string $header;

  /**
   * Constructs a new ContentAsideElement object.
   *
   * @param string $type
   *   An aside type.
   * @param string|null $header
   *   An aside header.
   */
  public function __construct(string $type, ?string $header = NULL) {
    $this->type = $type;
    $this->header = $header;
  }

  /**
   * Gets type.
   *
   * @return string
   *   A type.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Gets header.
   *
   * @return string|null
   *   A header.
   */
  public function getHeader(): ?string {
    return $this->header;
  }

}
