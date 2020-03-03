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
  protected $realpath;

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
   * The relative pathname.
   *
   * @var string
   */
  protected $relativePathname;

  /**
   * Constructs a new SourceContent object.
   *
   * @param string $realpath
   *   The content URI path.
   * @param string $relative_pathname
   *   The relative pathname.
   * @param string $language
   *   The content language.
   */
  public function __construct(string $realpath, string $relative_pathname, string $language) {
    $this->realpath = $realpath;
    $this->relativePathname = $relative_pathname;
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
      $this->file = new SplFileInfo($this->realpath);
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
    return file_get_contents($this->getRealpath());
  }

  /**
   * Gets absolute path to the file.
   *
   * @return string
   *   The URI path to content source.
   */
  public function getRealpath(): string {
    return $this->realpath;
  }

  /**
   * Gets relative pathname.
   *
   * The path must be relative to content source (git root). It will include
   * full path including docs folder name and langcode.
   *
   * @return string
   *   The relative pathname.
   */
  public function getRelativePathname(): string {
    return $this->relativePathname;
  }

  /**
   * The source content language.
   *
   * @return string
   *   The langcode.
   */
  public function getLanguage(): string {
    return $this->language;
  }

  /**
   * {@inheritdoc}
   */
  public function __sleep() {
    $vars = get_object_vars($this);
    // SplFileInfo is not serializable and don't need to be serialized.
    unset($vars['file']);
    return array_keys($vars);
  }

}
