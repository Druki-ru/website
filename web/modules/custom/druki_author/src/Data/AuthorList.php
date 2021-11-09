<?php

declare(strict_types=1);

namespace Drupal\druki_author\Data;

/**
 * Provides a list of authors.
 */
final class AuthorList implements \IteratorAggregate {

  /**
   * An array with authors.
   *
   * @var \Drupal\druki_author\Data\Author[]
   */
  protected array $authors = [];

  /**
   * Adds an author to the list.
   *
   * @param \Drupal\druki_author\Data\Author $author
   *   The author object.
   *
   * @return $this
   */
  public function addAuthor(Author $author): self {
    $this->authors[] = $author;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->authors);
  }

}
