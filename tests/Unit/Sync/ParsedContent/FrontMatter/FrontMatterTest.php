<?php

namespace Druki\Tests\Unit\Sync\ParsedContent\FrontMatter;

use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatter;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValueInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for FrontMatter.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatter
 */
final class FrontMatterTest extends UnitTestCase {

  /**
   * Tests the basic behaviors under normal conditions.
   */
  public function test(): void {
    $value_1 = $this->prophesizeFrontMatterProphecy('slug', 'drupal');
    $value_2 = $this->prophesizeFrontMatterProphecy('title', 'Hello World!');

    $front_matter = new FrontMatter();
    $front_matter->add($value_1);
    // At this point it's invalid.
    $this->assertFalse($front_matter->valid());
    $returned_value = $front_matter->add($value_2);
    $this->assertTrue($front_matter->valid());
    // Test fluent setter.
    $this->assertSame($front_matter, $returned_value);

    $this->assertSame($value_1, $front_matter->get('slug'));
    $this->assertSame($value_2, $front_matter->get('title'));
    $this->assertNull($front_matter->get('foo-bar'));
    $this->assertTrue($front_matter->has('slug'));
    $this->assertFalse($front_matter->has('foo-bar'));
    $this->assertSame([$value_1, $value_2], $front_matter->getValues());
  }

  /**
   * Prepare prophecy for FrontMatterValue.
   *
   * @param string $key
   *   The front matter key.
   * @param string $value
   *   The front matter value.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValueInterface
   *   The prophecy.
   */
  protected function prophesizeFrontMatterProphecy(string $key, string $value): FrontMatterValueInterface {
    $instance = $this->prophesize(FrontMatterValueInterface::class);
    $instance->getKey()->willReturn($key);
    $instance->getValue()->willReturn($value);
    return $instance->reveal();
  }

}
