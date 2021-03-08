<?php

namespace Drupal\druki_content\Redirect;

/**
 * Provides list with redirect files.
 */
final class RedirectFileList implements \IteratorAggregate {

  /**
   * The list of redirect files.
   *
   * @var \Drupal\druki_content\Redirect\RedirectFile
   */
  protected $files = [];

  /**
   * Adds file to the list.
   *
   * @param \Drupal\druki_content\Redirect\RedirectFile $file
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
  public function getIterator() {
    return new \ArrayIterator($this->files);
  }

}
