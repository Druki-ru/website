<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\ContentCodeElement;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content code element.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentCodeElement
 */
final class ContentCodeElementTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $element = new ContentCodeElement('Hello World!');
    $this->assertEquals('Hello World!', $element->getContent());
    $this->assertNull($element->getLanguage());

    $element = new ContentCodeElement('Hello, World!', 'php');
    $this->assertEquals('php', $element->getLanguage());
  }

}
