<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Utility;

use Drupal\druki\Utility\PathUtils;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for path utils.
 *
 * @coversDefaultClass \Drupal\druki\Utility\PathUtils
 */
final class PathUtilsTest extends UnitTestCase {

  /**
   * Tests that normalization works as expected.
   *
   * @param string $in
   *   The input value.
   * @param string $out
   *   The output value.
   *
   * @dataProvider normalizePathProvider
   */
  public function testNormalizePath(string $in, string $out): void {
    $this->assertEquals($out, PathUtils::normalizePath($in));
  }

  /**
   * Provides testing data for testing path normalization.
   */
  public function normalizePathProvider(): array {
    return [
      'default' => ['foo/bar/test.md', 'foo/bar/test.md'],
      'backward' => ['foo/bar/test/../../test.md', 'foo/test.md'],
      'current' => ['foo/bar/./test.md', 'foo/bar/test.md'],
    ];
  }

}
