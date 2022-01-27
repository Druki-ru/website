<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Data;

use Drupal\druki_content\Data\Content;
use Drupal\druki_content\Data\ContentElementBase;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for structured content object.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\Content
 */
final class ContentTest extends UnitTestCase {

  /**
   * Tests that objects behave as expected.
   */
  public function testObject(): void {
    $content = new Content();

    $this->assertInstanceOf(\ArrayIterator::class, $content->getElements());
    $this->assertEquals(0, $content->getElements()->count());
    $this->assertEquals([], $content->getElements()->getArrayCopy());

    $element = new class() extends ContentElementBase {

      /**
       * Tests return value.
       *
       * @return string
       *   A value to assert.
       */
      public function sayHello(): string {
        return 'Hello World!';
      }

    };
    $content->addElement($element);

    $this->assertEquals(1, $content->getElements()->count());
    $this->assertEquals('Hello World!', $content->getElements()->offsetGet(0)->sayHello());
  }

}
