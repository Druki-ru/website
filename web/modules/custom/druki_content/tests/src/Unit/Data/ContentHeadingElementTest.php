<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Data;

use Drupal\druki_content\Data\ContentHeadingElement;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content heading element.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentHeadingElement
 */
final class ContentHeadingElementTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $this->expectException(\InvalidArgumentException::class);
    new ContentHeadingElement(0, 'Test');

    $this->expectException(\InvalidArgumentException::class);
    new ContentHeadingElement(7, 'Test');

    $element = new ContentHeadingElement(1, 'Hello World!');
    $this->assertEquals(1, $element->getLevel());
    $this->assertEquals('Hello World!', $element->getContent());
  }

}
