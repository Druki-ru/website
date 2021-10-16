<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentBlockInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for structured content object.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\Content
 */
final class ContentTest extends UnitTestCase {

  /**
   * Tests that objects behave as expected.
   *
   * @return void
   */
  public function testObject(): void {
    $content = new Content();

    $this->assertInstanceOf(\ArrayIterator::class, $content->getBlocks());
    $this->assertEquals(0, $content->getBlocks()->count());
    $this->assertEquals([], $content->getBlocks()->getArrayCopy());

    $block = new class() implements ContentBlockInterface {
      public function sayHello(): string {
        return 'Hello World!';
      }
    };
    $content->addBlock($block);

    $this->assertEquals(1, $content->getBlocks()->count());
    $this->assertEquals('Hello World!', $content->getBlocks()->offsetGet(0)->sayHello());
  }

}
