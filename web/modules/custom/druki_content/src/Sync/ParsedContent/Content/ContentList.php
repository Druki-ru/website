<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

/**
 * Provides value object for store list of content.
 */
final class ContentList implements \IteratorAggregate {

  /**
   * The content array.
   *
   * @var array
   */
  private array $content = [];

  /**
   * Adds content to list.
   *
   * @param \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphContentInterface $content
   *   The content instance.
   *
   * @return $this
   */
  public function add(ParagraphContentInterface $content): ContentList {
    $this->content[] = $content;

    return $this;
  }

  /**
   * Set the internal pointer of list to its last element.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphContentInterface
   *   The last content element if exists.
   */
  public function end(): ?ParagraphContentInterface {
    $last_element = \end($this->content);

    if ($last_element instanceof ParagraphContentInterface) {
      return $last_element;
    }
    else {
      return NULL;
    }
  }

  /**
   * Pop the content off the end of list.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphContentInterface|null
   *   The popped content element.
   */
  public function pop(): ?ParagraphContentInterface {
    $last_element = \array_pop($this->content);

    if ($last_element instanceof ParagraphContentInterface) {
      return $last_element;
    }
    else {
      return NULL;
    }
  }

  /**
   * Retrieve an external iterator.
   *
   * @return \ArrayIterator
   *   The iterator for array.
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->content);
  }

  /**
   * Gets count of content elements.
   *
   * @return int
   *   The content count value.
   */
  public function count(): int {
    return \count($this->content);
  }

}
