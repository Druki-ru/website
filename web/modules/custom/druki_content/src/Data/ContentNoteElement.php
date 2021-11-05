<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides content element for notes.
 */
final class ContentNoteElement extends ContentElementBase {

  /**
   * The note type.
   */
  protected string $type;

  /**
   * Constructs a new ContentNoteElement object.
   *
   * @param string $type
   *   The note type.
   */
  public function __construct(string $type) {
    $allowed_types = ['note', 'warning', 'tip', 'important'];
    if (!\in_array($type, $allowed_types)) {
      $error = \sprintf(
        "The content note element expected to be one of these type '%s', but '%s' if given.",
        \implode(', ', $allowed_types),
        $type,
      );
      throw new \InvalidArgumentException($error);
    }
    $this->type = $type;
  }

  /**
   * Gets note type.
   *
   * @return string
   *   The note type.
   */
  public function getType(): string {
    return $this->type;
  }

}
