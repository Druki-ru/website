<?php

namespace Drupal\druki_redirect\Data;

/**
 * Provides list with redirect files.
 */
final class RedirectFileList implements \IteratorAggregate {

  /**
   * The list of redirect files.
   */
  protected array $files = [];

  /**
   * Adds file to the list.
   *
   * @param \Drupal\druki_redirect\Data\RedirectFile $file
   *   The redirect file.
   *
   * @return $this
   */
  public function addFile(RedirectFile $file): self {
    $this->files[$file->getHash()] = $file;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->files);
  }

}
