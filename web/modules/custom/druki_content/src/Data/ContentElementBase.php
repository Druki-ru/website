<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;

/**
 * Provides abstract implementation for content block.
 */
abstract class ContentElementBase implements ContentElementInterface {

  /**
   * The parent element.
   */
  protected ?ContentElementInterface $parent = NULL;

  /**
   * An array with children.
   *
   * @var \Drupal\druki_content\Data\ContentElementInterface[]
   */
  protected array $children = [];

  /**
   * {@inheritdoc}
   */
  public function hasParent(): bool {
    return !\is_null($this->parent);
  }

  /**
   * {@inheritdoc}
   */
  public function getParent(): ?ContentElementInterface {
    return $this->parent;
  }

  /**
   * {@inheritdoc}
   */
  public function setParent(ContentElementInterface $element): ContentElementInterface {
    $this->parent = $element;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addChild(ContentElementInterface $element): ContentElementInterface {
    $this->children[] = $element;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChildren(): \ArrayIterator {
    return new \ArrayIterator($this->children);
  }

  /**
   * {@inheritdoc}
   */
  public function hasChildren(): bool {
    return (bool) \count($this->children);
  }

}
