<?php

declare(strict_types=1);

namespace Drupal\druki_author\Data;

/**
 * Provides an object to store 'authors.json' file metadata.
 */
final class AuthorsFile {

  /**
   * The path to file.
   */
  protected string $pathname;

  /**
   * Constructs a new AuthorsFile object.
   *
   * @param string $pathname
   *   The path to 'authors.json' file.
   */
  public function __construct(string $pathname) {
    if (!\file_exists($pathname)) {
      $message = \sprintf("The file %s doesn't exists.", $pathname);
      throw new \InvalidArgumentException($message);
    }
    $this->pathname = $pathname;
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
