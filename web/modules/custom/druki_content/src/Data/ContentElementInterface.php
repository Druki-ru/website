<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides interface for single content element.
 *
 * The content element — a single typed content data. E.g., image, text, code.
 */
interface ContentElementInterface {

  /**
   * Sets parent element if current is child.
   *
   * @param \Drupal\druki_content\Data\ContentElementInterface $element
   *   The parent element.
   *
   * @return $this
   */
  public function setParent(ContentElementInterface $element): self;

  /**
   * Checks a current element for a parent.
   *
   * @return bool
   *   TRUE if has a parent, FALSE otherwise.
   */
  public function hasParent(): bool;

  /**
   * Gets parent element.
   *
   * @return \Drupal\druki_content\Data\ContentElementInterface|null
   *   The parent element.
   */
  public function getParent(): ?ContentElementInterface;

  /**
   * Adds child element.
   *
   * @param \Drupal\druki_content\Data\ContentElementInterface $element
   *   The child element.
   *
   * @return $this
   */
  public function addChild(ContentElementInterface $element): self;

  /**
   * Gets children elements.
   *
   * @return \ArrayIterator
   *   An array with children.
   */
  public function getChildren(): \ArrayIterator;

  /**
   * Checks is a current element has children.
   *
   * @return bool
   *   TRUE if element have children.
   */
  public function hasChildren(): bool;

}
