<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\ContentHeadingElement;
use Drupal\druki_content\Data\ContentNoteElement;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content note element.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentNoteElement
 */
final class ContentNoteElementTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $this->expectException(\InvalidArgumentException::class);
    new ContentNoteElement('foo');

    $element = new ContentNoteElement('warning');
    $this->assertEquals('warning', $element->getType());
  }

}
