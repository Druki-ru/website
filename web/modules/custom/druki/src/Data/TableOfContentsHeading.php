<?php

declare(strict_types=1);

namespace Drupal\druki\Data;

/**
 * Provides heading for table of contents.
 */
final class TableOfContentsHeading {

  /**
   * The link text.
   */
  protected string $text;

  /**
   * The link level according to Heading levels.
   */
  protected int $level;

  /**
   * Constructs a new TableOfContentsLink object.
   *
   * @param string $text
   *   The link text.
   * @param int $level
   *   The link level.
   */
  public function __construct(string $text, int $level) {
    $this->text = $text;
    if ($level < 2 || $level > 6) {
      $message = \sprintf('The link headed must be in range of 2-6, %s provided.', $level);
      throw new \InvalidArgumentException($message);
    }
    $this->level = $level;
  }

  /**
   * Gets link level.
   *
   * @return int
   *   The link level.
   */
  public function getLevel(): int {
    return $this->level;
  }

  /**
   * Gets link text.
   *
   * @return string
   *   The link text.
   */
  public function getText(): string {
    return $this->text;
  }

}
