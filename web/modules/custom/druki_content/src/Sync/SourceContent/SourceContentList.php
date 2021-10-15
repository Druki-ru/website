<?php

namespace Drupal\druki_content\Sync\SourceContent;

/**
 * Provides value object to store multiple source content.
 *
 * @todo Refactor to ContentSourceFileList.
 */
final class SourceContentList implements \IteratorAggregate {

  /**
   * The array with content sources.
   *
   * @var \Drupal\druki_content\Sync\SourceContent\SourceContent[]
   */
  protected array $items = [];

  /**
   * Split items into chunks.
   *
   * @param int $size
   *   The size of each chunk.
   *
   * @return \Drupal\druki_content\Sync\SourceContent\SourceContentList[]
   *   An array with SourceContentList contains no more than size of items.
   */
  public function chunk(int $size): array {
    $chunks = \array_chunk($this->items, $size);
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
   * @param \Drupal\druki_content\Sync\SourceContent\SourceContent $source_content
   *   The content source item.
   *
   * @return $this
   */
  public function add(SourceContent $source_content): SourceContentList {
    $this->items[] = $source_content;
    return $this;
  }

  /**
   * Returns source content items as array.
   *
   * @return \Drupal\druki_content\Sync\SourceContent\SourceContent[]
   *   An array with source content items.
   */
  public function toArray(): array {
    return $this->items;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return new \ArrayIterator($this->items);
  }

  /**
   * Gets number of source content items.
   *
   * @return int
   *   The number of items.
   */
  public function numberOfItems(): int {
    return \count($this->items);
  }

}
