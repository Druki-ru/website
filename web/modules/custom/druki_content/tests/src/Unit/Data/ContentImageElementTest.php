<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Data;

use Drupal\druki_content\Data\ContentImageElement;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content image element.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentImageElement
 */
final class ContentImageElementTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $src = 'https://example.com/img.jpg';
    $alt = 'This is an image!';
    $element = new ContentImageElement($src, $alt);
    $this->assertEquals($src, $element->getSrc());
    $this->assertEquals($alt, $element->getAlt());
  }

}
