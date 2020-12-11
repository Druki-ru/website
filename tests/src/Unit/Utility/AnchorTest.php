<?php

namespace Druki\Tests\Unit\Utility;

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
   * @dataProvider anchorProvider
   * @covers ::generate
   */
  public function testGenerator($text, $id, $duplicate_mode, $expected) {
    $actual = Anchor::generate($text, $id, $duplicate_mode);
    $this->assertSame($expected, $actual);
  }

  /**
   * The anchor text values provider.
   *
   * @return array
   *   The array with data for testing.
   */
  public function anchorProvider() {
    return [
      'reusable for "test"' => ['test', 'default', Anchor::REUSE, 'test'],
      'reusable for "test" repeat' => ['test', 'default', Anchor::REUSE, 'test'],
      'reusable for "test2"' => ['test2', 'default', Anchor::REUSE, 'test2'],
      'counter for "test"' => ['test', 'counter', Anchor::COUNTER, 'test'],
      'counter for "test" repeat' => ['test', 'counter', Anchor::COUNTER, 'test-1'],
      'counter for "test2"' => ['test2', 'counter', Anchor::COUNTER, 'test2'],
    ];
  }

}
