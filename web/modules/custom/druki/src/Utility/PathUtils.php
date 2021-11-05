<?php

declare(strict_types=1);

namespace Drupal\druki\Utility;

/**
 * Provides system path utils.
 */
final class PathUtils {

  /**
   * Normalize path like standard realpath() does.
   *
   * The main difference is that this implementation doesn't care about file or
   * directory existance, it's just working with path.
   *
   * E.g., 'path/to/something/../../file.md' will be convert to 'path/file.md'.
   *
   * @param string $path
   *   The path to process.
   *
   * @return string
   *   The path.
   *
   * @see https://stackoverflow.com/a/10067975/4751623
   */
  public static function normalizePath(string $path): string {
    $root = $path[0] === '/' ? '/' : '';

    $segments = \explode('/', \trim($path, '/'));
    $ret = [];
    foreach ($segments as $segment) {
      if (($segment == '.') || \strlen($segment) === 0) {
        continue;
      }
      if ($segment == '..') {
        \array_pop($ret);
      }
      else {
        \array_push($ret, $segment);
      }
    }

    return $root . \implode('/', $ret);
  }

}
