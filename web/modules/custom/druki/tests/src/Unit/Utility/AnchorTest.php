<?php

namespace Drupal\Tests\druki\Unit\Utility;

use Drupal\druki\Utility\Anchor;
use Drupal\Tests\UnitTestCase;

/**
 * Tests text utility object.
 *
 * @coversDefaultClass \Drupal\druki\Utility\Anchor
 */
class AnchorTest extends UnitTestCase {

  /**
   * Tests anchor generation.
   *
   * @param string $text
   *   A text to process.
   * @param string $id
   *   An anchor ID.
   * @param int $duplicate_mode
   *   A duplication mode.
   * @param string $expected
   *   An expected result.
   *
   * @dataProvider anchorProvider
   * @covers ::generate
   */
  public function testGenerator(string $text, string $id, int $duplicate_mode, string $expected): void {
    $actual = Anchor::generate($text, $id, $duplicate_mode);
    $this->assertSame($expected, $actual);
  }

  /**
   * The anchor text values provider.
   *
   * @return array
   *   The array with data for testing.
   */
  public function anchorProvider(): array {
    return [
      'reusable for "test"' => ['test', 'default', Anchor::REUSE, 'test'],
      'reusable for "test" 1' => ['test', 'default', Anchor::REUSE, 'test'],
      'reusable for "test2"' => ['test2', 'default', Anchor::REUSE, 'test2'],
      'counter for "test"' => ['test', 'counter', Anchor::COUNTER, 'test'],
      'counter for "test" 1' => ['test', 'counter', Anchor::COUNTER, 'test-1'],
      'counter for "test2"' => ['test2', 'counter', Anchor::COUNTER, 'test2'],
    ];
  }

}
