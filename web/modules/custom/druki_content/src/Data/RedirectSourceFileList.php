<?php

namespace Drupal\druki_content\Data;

/**
 * Provides list with redirect files.
 */
final class RedirectSourceFileList implements \IteratorAggregate {

  /**
   * The list of redirect files.
   */
  protected array $files = [];

  /**
   * Adds file to the list.
   *
   * @param \Drupal\druki_content\Data\RedirectSourceFile $file
   *   The redirect file.
   *
   * @return $this
   */
  public function addFile(RedirectSourceFile $file): self {
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
