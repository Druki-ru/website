<?php

namespace Drupal\druki_content\SourceContent;

use ArrayIterator;
use IteratorAggregate;

/**
 * Provides value object to store multiple source content.
 */
final class SourceContentList implements IteratorAggregate {

  /**
   * The array with content sources.
   *
   * @var \Drupal\druki_content\SourceContent\SourceContent[]
   */
  protected $items = [];

  /**
   * Split items into chunks.
   *
   * @param int $size
   *   The size of each chunk.
   *
   * @return \Drupal\druki_content\SourceContent\SourceContentList[]
   *   An array with SourceContentList contains no more than size of items.
   */
  public function chunk(int $size): array {
    $chunks = array_chunk($this->items, $size);
    $result = [];
    foreach ($chunks as $chunk) {
      $list = new SourceContentList();
      foreach ($chunk as $item) {
        $list->add($item);
      }
      $result[] = $list;
    }

    return $result;
  }

  /**
   * Adds content source item to collection.
   *
   * @param \Drupal\druki_content\SourceContent\SourceContent $source_content
   *   The content source item.
   *
   * @return $this
   */
  public function add(SourceContent $source_content): SourceContentList {
    $this->items[] = $source_content;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return new ArrayIterator($this->items);
  }

  /**
   * Gets number of source content items.
   *
   * @return int
   *   The number of items.
   */
  public function numberOfItems(): int {
    return count($this->items);
  }

}
