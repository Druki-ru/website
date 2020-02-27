<?php

namespace Drupal\druki_content\Sync;

use SplFileInfo;

/**
 * Provides value object stores single item for synchronization.
 */
final class SyncItem {

  /**
   * The source content path URI.
   *
   * @var string
   */
  protected $uri;

  /**
   * The source content language.
   *
   * @var string
   */
  protected $language;

  /**
   * The source file information.
   *
   * @var null|\SplFileInfo
   */
  protected $file = NULL;

  /**
   * Constructs a new SyncItem object.
   *
   * @param string $uri
   *   The content URI path.
   * @param string $language
   *   The content language.
   */
  public function __construct(string $uri, string $language) {
    $this->uri = $uri;
    $this->language = $language;
  }

  /**
   * Gets file information.
   *
   * @return \SplFileInfo
   *   The file information object.
   */
  protected function getFile(): SplFileInfo {
    if (!$this->file) {
      $this->file = new SplFileInfo($this->uri);
    }

    return $this->file;
  }

}
