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
  protected array $elements = [];

  /**
   * Adds content element.
   *
   * @param \Drupal\druki_content\Data\ContentElementInterface $element
   *   The content element.
   *
   * @return $this
   */
  public function addElement(ContentElementInterface $element): self {
    $this->elements[] = $element;
    return $this;
  }

  /**
   * Gets elements.
   *
   * @return \ArrayIterator
   *   An array iterator for elements.
   */
  public function getElements(): \ArrayIterator {
    return new \ArrayIterator($this->elements);
  }

}
