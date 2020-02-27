<?php

namespace Drupal\druki_content\Sync;

use ArrayIterator;
use IteratorAggregate;

/**
 * Provides
 */
final class SyncItems implements IteratorAggregate {

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return new ArrayIterator([]);
  }

}
