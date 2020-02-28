<?php

namespace Drupal\druki_content\SourceContent;

use SplFileInfo;

/**
 * Provides value object stores single source content.
 */
final class SourceContent {

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
   * Constructs a new SourceContent object.
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
   * Gets content source URI.
   *
   * @return string
   *   The URI path to content source.
   */
  public function getUri(): string {
    return $this->uri;
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
