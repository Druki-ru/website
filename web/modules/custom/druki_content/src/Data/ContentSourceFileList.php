<?php

namespace Drupal\druki_content\Data;

/**
 * Provides value object to store multiple content source files.
 */
final class ContentSourceFileList implements \IteratorAggregate {

  /**
   * The array with content sources.
   *
   * @var \Drupal\druki_content\Data\ContentSourceFile[]
   */
  protected array $items = [];

  /**
   * Adds content source item to collection.
   *
   * @param \Drupal\druki_content\Data\ContentSourceFile $source_content
   *   The content source item.
   *
   * @return $this
   */
  public function addFile(ContentSourceFile $source_content): ContentSourceFileList {
    $this->items[] = $source_content;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return new \ArrayIterator($this->items);
  }

}
