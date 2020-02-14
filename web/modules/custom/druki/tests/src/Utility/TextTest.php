<?php

namespace Drupal\Tests\druki\Unit;

use Drupal\druki\Utility\Text;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests text utility object.
 */
class TextTest extends KernelTestBase {

  /**
   * Tests anchor generation.
   *
   * @dataProvider anchorProvider
   */
  public function testAnchor($text, $expected) {
    $actual = Text::anchor($text);
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
      ['test', 'test'],
    ];
  }

}
