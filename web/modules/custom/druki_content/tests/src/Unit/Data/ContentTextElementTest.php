<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Data;

use Drupal\druki_content\Data\ContentTextElement;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content text element.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentTextElement
 */
final class ContentTextElementTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $element = new ContentTextElement('Hello World!');
    $this->assertEquals('Hello World!', $element->getContent());
  }

}
