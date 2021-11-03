<?php

declare(strict_types=1);

namespace Drupal\druki_redirect\Data;

use Drupal\Component\Utility\UrlHelper;

/**
 * Provides value object with redirect URL.
 */
final class RedirectUrl {

  /**
   * The redirect URL path.
   */
  protected string $path;

  /**
   * The redirect URL query.
   */
  protected array $query;

  /**
   * The redirect URL fragment.
   */
  protected string $fragment;

  /**
   * Constructs a new RedirectUrl object.
   *
   * @param string $path
   *   The redirect URL path.
   * @param array $query
   *   The redirect URL query.
   * @param string $fragment
   *   The redirect URL fragment.
   */
  public function __construct(string $path, array $query = [], string $fragment = '') {
    $this->path = $path;
    $this->query = $query;
    $this->fragment = $fragment;
  }

  /**
   * Build redirect URL object from user input.
   *
   * @param string $url
   *   The URL.
   *
   * @return self
   *   The instance of redirect URL.
   */
  public static function buildFromUserInput(string $url): self {
    $parsed_url = UrlHelper::parse(\trim($url));
    return new self($parsed_url['path'], $parsed_url['query'], $parsed_url['fragment']);
  }

  /**
   * Gets redirect URL path.
   *
   * @return string
   *   The path.
   */
  public function getPath(): string {
    return $this->path;
  }

  /**
   * Gets redirect URL query.
   *
   * @return array
   *   The query parameters.
   */
  public function getQuery(): array {
    return $this->query;
  }

  /**
   * Gets redirect URL fragment.
   *
   * @return array
   *   The options.
   */
  public function getFragment(): string {
    return $this->fragment;
  }

}
