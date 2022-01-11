<?php

declare(strict_types=1);

namespace Drupal\druki\Data;

/**
 * Provides a collection with Contributor value objects.
 */
final class ContributorList implements \IteratorAggregate {

  /**
   * An array with contributor items.
   */
  protected array $items = [];

  /**
   * Adds a contributor to a list.
   *
   * @param \Drupal\druki\Data\Contributor $contributor
   *   A contributor information.
   *
   * @return $this
   */
  public function addContributor(Contributor $contributor): self {
    $this->items[] = $contributor;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

}
