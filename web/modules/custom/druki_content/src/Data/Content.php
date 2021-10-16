<?php

declare(strict_types=1);

namespace Drupal\druki_content\Data;


/**
 * Provides structured content.
 *
 * @todo Complete it.
 */
final class Content implements \IteratorAggregate {

  protected array $blocks = [];

  public function addBlock() {

  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->blocks);
  }

}
