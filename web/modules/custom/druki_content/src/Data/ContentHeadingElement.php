<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides content element for headings.
 */
final class ContentHeadingElement extends ContentElementBase {

  /**
   * The heading level.
   */
  protected int $level;

  /**
   * The heading value.
   */
  protected string $content;

  /**
   * Constructs a new ContentHeadingElement object.
   *
   * @param int $level
   *   The heading level.
   * @param string $content
   *   The heading value.
   */
  public function __construct(int $level, string $content) {
    if ($level < 1 || $level > 6) {
      $error = \sprintf('The content heading element level should have level from 1 to 6, %s given.', $level);
      throw new \InvalidArgumentException($error);
    }
    $this->level = $level;
    $this->content = $content;
  }

  /**
   * Gets heading level.
   *
   * @return int
   *   The heading level.
   */
  public function getLevel(): int {
    return $this->level;
  }

  /**
   * Gets heading value.
   *
   * @return string
   *   The heading value.
   */
  public function getContent(): string {
    return $this->content;
  }

}
