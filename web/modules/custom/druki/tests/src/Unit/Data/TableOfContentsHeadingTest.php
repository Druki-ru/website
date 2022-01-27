<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Unit\Data;

use Drupal\druki\Data\TableOfContentsHeading;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for Table of Contents link.
 *
 * @coversDefaultClass \Drupal\druki\Data\TableOfContentsHeading
 */
final class TableOfContentsHeadingTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $link = new TableOfContentsHeading('Hello, World!', 2);
    $this->assertEquals(2, $link->getLevel());
    $this->assertEquals('Hello, World!', $link->getText());

    $this->expectException(\InvalidArgumentException::class);
    new TableOfContentsHeading('Hello, World!', 0);
  }

}
