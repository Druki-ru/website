<?php

declare(strict_types=1);

namespace Drupal\druki\Data;

/**
 * Provides table of contents storage.
 */
final class TableOfContents implements \IteratorAggregate {

  /**
   * An array with ToC headings.
   *
   * @var \Drupal\druki\Data\TableOfContentsHeading[]
   */
  protected array $headings = [];

  /**
   * Adds link into ToC.
   *
   * @param \Drupal\druki\Data\TableOfContentsHeading $heading
   *   The ToC link.
   *
   * @return $this
   */
  public function addHeading(TableOfContentsHeading $heading): self {
    $this->headings[] = $heading;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->headings);
  }

  /**
   * Gets and tree array with headings.
   *
   * ToC stored as flat array, but for generating tree much easier from the same
   * array tree. This method will return ToC headings in hierarchical array
   * where children headings are inside parents array item.
   *
   * @return array
   *   An array tree.
   */
  public function toTreeArray(): array {
    $flat_tree = [];
    $link_id = 1;
    foreach ($this->headings as $heading) {
      $flat_tree[] = $this->buildFlatLink($heading, $link_id, $flat_tree);
      $link_id++;
    }
    return $this->buildTree($flat_tree);
  }

  /**
   * Builds a single link for flat tree.
   *
   * @param \Drupal\druki\Data\TableOfContentsHeading $heading
   *   The current heading.
   * @param int $link_id
   *   The current heading link ID.
   * @param array $tree
   *   The currently prepare flat tree.
   *
   * @return array
   *   An array with flat link item.
   */
  protected function buildFlatLink(TableOfContentsHeading $heading, int $link_id, array $tree = []): array {
    $parent_id = 0;
    foreach (\array_reverse($tree) as $tree_item) {
      if ($tree_item['heading']->getLevel() < $heading->getLevel()) {
        $parent_id = $tree_item['id'];
        break;
      }
    }

    return [
      'heading' => $heading,
      'id' => $link_id,
      'parent_id' => $parent_id,
    ];
  }

  /**
   * Builds ToC tree.
   *
   * @param array $flat_tree
   *   An array with flat tree.
   * @param int $parent_id
   *   The parent ID.
   *
   * @return array
   *   An array with headings and their children.
   */
  protected function buildTree(array $flat_tree, int $parent_id = 0): array {
    $tree = [];
    foreach ($flat_tree as $tree_item) {
      if ($tree_item['parent_id'] != $parent_id) {
        continue;
      }
      $tree[] = [
        'heading' => $tree_item['heading'],
        'children' => $this->buildTree($flat_tree, $tree_item['id']),
      ];
    }
    return $tree;
  }

}
