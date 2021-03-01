<?php

namespace Druki\Tests\Unit\Sync\SourceContent;

use Drupal\druki_content\Sync\SourceContent\SourceContent;
use Drupal\druki_content\Sync\SourceContent\SourceContentList;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for list value object contains source contents.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\SourceContent\SourceContentList
 */
final class SourceContentListTest extends UnitTestCase {

  /**
   * Test the class.
   */
  public function testList(): void {
    $source_content_list = new SourceContentList();

    $source_content_1 = new SourceContent('foo', 'bar', 'ru');
    $source_content_2 = new SourceContent('foo', 'bar', 'ru');

    $return = $source_content_list->add($source_content_1);
    $this->assertSame($source_content_list, $return);

    $source_content_list->add($source_content_2);
    $this->assertEquals(2, $source_content_list->numberOfItems());

    $expected_array = [
      $source_content_1,
      $source_content_2,
    ];
    $this->assertEquals($expected_array, $source_content_list->toArray());

    $iterator = $source_content_list->getIterator();
    $this->assertTrue($iterator instanceof \ArrayIterator);
    $this->assertEquals(2, $iterator->count());
    $this->assertEquals($expected_array, $iterator->getArrayCopy());
    
    $expected_chunks = [
      0 => (new SourceContentList())->add($source_content_1),
      1 => (new SourceContentList())->add($source_content_2),
    ];
    $this->assertEquals($expected_chunks, $source_content_list->chunk(1));
  }

}
