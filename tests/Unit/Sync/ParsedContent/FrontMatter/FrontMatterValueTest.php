<?php

namespace Druki\Tests\Unit\Sync\ParsedContent\FrontMatter;

use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for FrontMatterValue.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue
 */
final class FrontMatterValueTest extends UnitTestCase {

  /**
   * Tests basic value object behavior.
   */
  public function test(): void {
    $instance = new FrontMatterValue('id', 'drupal');
    $this->assertEquals('id', $instance->getKey());
    $this->assertEquals('drupal', $instance->getValue());
  }

}
