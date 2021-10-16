<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides structured content.
 */
final class Content {

  /**
   * An array with blocks.
   *
   * @var array
   */
  protected array $blocks = [];

  /**
   * Adds content block.
   *
   * @param \Drupal\druki_content\Data\ContentBlockInterface $block
   *   The content block.
   *
   * @return $this
   */
  public function addBlock(ContentBlockInterface $block): self {
    $this->blocks[] = $block;
    return $this;
  }

  /**
   * Gets blocks.
   *
   * @return \ArrayIterator
   *   An array iterator for blocks.
   */
  public function getBlocks(): \ArrayIterator {
    return new \ArrayIterator($this->blocks);
  }

}
