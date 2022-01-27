<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Data;

use Drupal\druki_content\Data\ContentElementBase;
use Drupal\Tests\UnitTestCase;

/**
 * Testing content element base.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentElementBase
 */
final class ContentElementBaseTest extends UnitTestCase {

  /**
   * Test that base class behave as expected.
   */
  public function testObject(): void {
    $root_element = new class() extends ContentElementBase {};
    $parent_element = new class() extends ContentElementBase {};
    $child1_element = new class() extends ContentElementBase {};
    $child2_element = new class() extends ContentElementBase {};

    $this->assertNull($root_element->getParent());
    $this->assertFalse($root_element->hasParent());
    $root_element->setParent($parent_element);
    $this->assertSame($parent_element, $root_element->getParent());
    $this->assertTrue($root_element->hasParent());

    $this->assertFalse($root_element->hasChildren());
    $this->assertSame(0, $root_element->getChildren()->count());
    $root_element->addChild($child1_element);
    $root_element->addChild($child2_element);
    $this->assertTrue($root_element->hasChildren());
    $this->assertEquals(2, $root_element->getChildren()->count());
    $this->assertSame($child1_element, $root_element->getChildren()->offsetGet(0));
    $this->assertSame($child2_element, $root_element->getChildren()->offsetGet(1));
  }

}
