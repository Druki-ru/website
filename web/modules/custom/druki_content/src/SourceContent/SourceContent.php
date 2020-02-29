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
   * Checks is files is readable.
   *
   * @return bool
   *   TRUE is readable, FALSE if file is not readable or not exists.
   */
  public function isReadable(): bool {
    return $this->getFile()->isReadable();
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

  /**
   * Gets file content.
   *
   * @return string
   *   The file content.
   */
  public function getContent(): string {
    return file_get_contents($this->getUri());
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

}
