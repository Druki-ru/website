<?php

namespace Drupal\druki_content\Data;

/**
 * Provides storage for redirects file.
 */
final class RedirectSourceFile {

  /**
   * The path to file.
   */
  protected string $pathname;

  /**
   * The langcode of redirects.
   */
  protected string $language;

  /**
   * RedirectSourceFile constructor.
   *
   * @param string $pathname
   *   The path to the file.
   * @param string $language
   *   The langcode for which these redirects are.
   */
  public function __construct(string $pathname, string $language) {
    if (!\file_exists($pathname)) {
      throw new \InvalidArgumentException("The file doesn't exists.");
    }
    $this->pathname = $pathname;
    $this->language = $language;
  }

  /**
   * Gets language.
   *
   * @return string
   *   The language redirects belongs to.
   */
  public function getLanguage(): string {
    return $this->language;
  }

  /**
   * Gets unique has for redirects.
   *
   * @return string
   *   The hash string.
   */
  public function getHash(): string {
    return \hash('sha256', \file_get_contents($this->getPathname()));
  }

  /**
   * Gets file pathname.
   *
   * @return string
   *   The file pathname.
   */
  public function getPathname(): string {
    return $this->pathname;
  }

}
