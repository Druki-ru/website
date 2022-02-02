<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Data;

use Drupal\druki_content\Data\ContentAsideElement;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a data value object test for aside element.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentAsideElement
 */
final class ContentAsideElementTest extends UnitTestCase {

  /**
   * Tests that objects behave as expected.
   */
  public function testObject(): void {
    $element = new ContentAsideElement('foo');
    $this->assertEquals('foo', $element->getType());
    $this->assertNull($element->getHeader());

    $element = new ContentAsideElement('bar', 'baz');
    $this->assertEquals('bar', $element->getType());
    $this->assertEquals('baz', $element->getHeader());
  }

}
