<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\ContentHeadingBlock;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content heading block.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentHeadingBlock
 */
final class ContentHeadingBlockTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   *
   * @return void
   */
  public function testObject(): void {
    $this->expectException(\InvalidArgumentException::class);
    new ContentHeadingBlock(0, 'Test');

    $this->expectException(\InvalidArgumentException::class);
    new ContentHeadingBlock(7, 'Test');

    $block = new ContentHeadingBlock(1, 'Hello World!');
    $this->assertEquals(1, $block->getLevel());
    $this->assertEquals('Hello World!', $block->getContent());
  }

}
