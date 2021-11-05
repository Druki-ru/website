<?php

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\ContentMetadata;
use Drupal\Tests\UnitTestCase;

/**
 * Tests content metadata value object.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentMetadata
 */
class ContentMetadataTest extends UnitTestCase {

  /**
   * Tests anchor generation.
   *
   * @covers ::createFromArray
   */
  public function testCreation(): void {
    $data = [];
    $this->expectException(\InvalidArgumentException::class);
    ContentMetadata::createFromArray($data);

    $data['title'] = 'This is Drupal!';
    $data['slug'] = 'drupal/about';
    $metadata = ContentMetadata::createFromArray($data);
    $this->assertEquals($data['title'], $metadata->getTitle());
    $this->assertEquals($data['slug'], $metadata->getSlug());

    $data['core'] = 's';
    $this->expectException(\InvalidArgumentException::class);
    ContentMetadata::createFromArray($data);

    $data['core'] = 9;
    $metadata = ContentMetadata::createFromArray($data);
    $this->assertEquals($data['core'], $metadata->getCore());

    $data['category'] = [];
    $this->expectException(\InvalidArgumentException::class);
    ContentMetadata::createFromArray($data);

    $data['category']['area'] = 'This is Drupal, again!';
    $metadata = ContentMetadata::createFromArray($data);
    $expected = [
      'area' => $data['category']['title'],
      'order' => 0,
      'title' => NULL,
    ];
    $this->assertEquals($expected, $metadata->getCategory());

    $data['category']['order'] = 17;
    $data['category']['title'] = 'Overriden title.';
    $this->assertEquals($data['category'], $metadata->getCategory());

    $data['search-keywords'] = 'This expected to be an array.';
    $this->expectException(\InvalidArgumentException::class);
    ContentMetadata::createFromArray($data);

    $data['search-keywords'] = ['foo', 'bar'];
    $metadata = ContentMetadata::createFromArray($data);
    $this->assertEquals($data['search-keywords'], $metadata->getSearchKeywords());

    $data['metatags'] = 'This expected to be an array.';
    $this->expectException(\InvalidArgumentException::class);
    ContentMetadata::createFromArray($data);

    $data['metatags'] = [
      'title' => 'The metatag title.',
      'description' => 'The metatag description.',
      'test' => '123',
    ];
    $metadata = ContentMetadata::createFromArray($data);
    $this->assertTrue($metadata->hasMetatag('title'));
    $this->assertTrue($metadata->hasMetatag('description'));
    $this->assertFalse($metadata->hasMetatag('test'));
    $this->assertEquals($data['metatags']['title'], $metadata->getMetatag('title'));
    $this->assertEquals($data['metatags']['description'], $metadata->getMetatag('description'));
    $this->assertNull($metadata->getMetatag('test'));
  }

}
