<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\ContentTextBlock;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content text block.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentTextBlock
 */
final class ContentTextBlockTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   *
   * @return void
   */
  public function testObject(): void {
    $block = new ContentTextBlock('Hello World!');
    $this->assertEquals('Hello World!', $block->getContent());
  }

}
