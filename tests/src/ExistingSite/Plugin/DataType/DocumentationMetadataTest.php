<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\DataType;

use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides a test for documentation metadata data type.
 *
 * @coversDefaultClass \Drupal\druki_content\Plugin\DataType\DocumentationMetadata
 */
final class DocumentationMetadataTest extends ExistingSiteBase {

  /**
   * Tests that data type works as expected.
   */
  public function testDataType(): void {
    /** @var \Drupal\Core\TypedData\TypedDataManagerInterface $typed_data_manager */
    $typed_data_manager = $this->container->get('typed_data_manager');
    /** @var \Drupal\druki_content\TypedData\DocumentationMetadataDefinition $definition */
    $definition = $typed_data_manager->createDataDefinition('druki_content_documentation_metadata');
    $definition->setRequired(TRUE);

    /** @var \Drupal\druki_content\Plugin\DataType\DocumentationMetadata $data_type */
    $data_type = $typed_data_manager->create($definition);
    $this->assertEquals(1, $data_type->validate()->count());
    $this->assertTrue($data_type->isEmpty());

    $data_type = $typed_data_manager->create($definition, []);
    $this->assertEquals(1, $data_type->validate()->count());
    $this->assertTrue($data_type->isEmpty());

    $data_type = $typed_data_manager->create($definition, [
      'foo' => 'bar',
    ]);
    $this->assertEquals(2, $data_type->validate()->count());
    $this->assertEquals('title', $data_type->validate()->get(0)->getPropertyPath());
    $this->assertEquals('slug', $data_type->validate()->get(1)->getPropertyPath());

    $metadata = [
      'title' => 'Drupal',
      'slug' => 'wiki/drupal',
    ];
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(0, $data_type->validate()->count());
    $this->assertEquals($metadata['title'], $data_type->getTitle());
    $this->assertEquals($metadata['slug'], $data_type->getSlug());
    $this->assertFalse($data_type->hasCore());
    $this->assertNull($data_type->getCore());
    $this->assertFalse($data_type->hasCategory());
    $this->assertNull($data_type->getCategory());
    $this->assertFalse($data_type->hasMetatags());
    $this->assertNull($data_type->getMetatags());
    $this->assertFalse($data_type->hasSearchKeywords());
    $this->assertNull($data_type->getSearchKeywords());
    $this->assertFalse($data_type->hasAuthors());
    $this->assertNull($data_type->getAuthors());

    $metadata['core'] = 1;
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(1, $data_type->validate()->count());

    $metadata['core'] = 100;
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(1, $data_type->validate()->count());

    $metadata['core'] = 9;
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(0, $data_type->validate()->count());
    $this->assertTrue($data_type->hasCore());
    $this->assertEquals(9, $data_type->getCore());

    $metadata['category'] = [
      'order' => -10,
    ];
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(2, $data_type->validate()->count());
    $this->assertEquals('category.area', $data_type->validate()->get(0)->getPropertyPath());
    $this->assertEquals('category.order', $data_type->validate()->get(1)->getPropertyPath());

    $metadata['category'] = [
      'area' => 'foo',
    ];
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(0, $data_type->validate()->count());
    $this->assertTrue($data_type->hasCategory());
    $expected = [
      'area' => 'foo',
      'order' => 0,
      'title' => NULL,
    ];
    $this->assertEquals($expected, $data_type->getCategory());

    $metadata['metatags'] = [
      'title' => 'Drupal',
      'description' => 'Hello, World!',
    ];
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(0, $data_type->validate()->count());
    $this->assertTrue($data_type->hasMetatags());
    $expected = [
      'title' => 'Drupal',
      'og_title' => 'Drupal',
      'twitter_cards_title' => 'Drupal',
      'description' => 'Hello, World!',
      'og_description' => 'Hello, World!',
    ];
    $this->assertEquals($expected, $data_type->getMetatags());
    $this->assertTrue($data_type->hasMetatag('title'));
    $this->assertFalse($data_type->hasMetatag('title-two'));
    $this->assertEquals('Drupal', $data_type->getMetatag('title'));
    $this->assertNull($data_type->getMetatag('title-two'));

    $metadata['search-keywords'] = ['foo', 'bar'];
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(0, $data_type->validate()->count());
    $this->assertTrue($data_type->hasSearchKeywords());
    $this->assertEquals($metadata['search-keywords'], $data_type->getSearchKeywords());

    $metadata['authors'] = ['Dries', 'zuck'];
    $data_type = $typed_data_manager->create($definition, $metadata);
    $this->assertEquals(0, $data_type->validate()->count());
    $this->assertTrue($data_type->hasAuthors());
    $this->assertEquals($metadata['authors'], $data_type->getAuthors());
  }

  /**
   * Tests that checksum generator works as expected.
   */
  public function testChecksum(): void {
    /** @var \Drupal\Core\TypedData\TypedDataManagerInterface $typed_data_manager */
    $typed_data_manager = $this->container->get('typed_data_manager');
    /** @var \Drupal\druki_content\TypedData\DocumentationMetadataDefinition $definition */
    $definition = $typed_data_manager->createDataDefinition('druki_content_documentation_metadata');
    $definition->setRequired(TRUE);

    /** @var \Drupal\druki_content\Plugin\DataType\DocumentationMetadata $data_type */
    $data_type = $typed_data_manager->create($definition, [
      'title' => 'Foo',
      'slug' => 'bar',
    ]);
    $checksum_first = $data_type->checksum();
    // Ensure that it always returns the same result for the same data.
    $this->assertEquals($checksum_first, $data_type->checksum());

    $data_type = $typed_data_manager->create($definition, [
      'slug' => 'bar',
      'title' => 'Foo',
    ]);
    // Order of values shouldn't affect result.
    $this->assertEquals($checksum_first, $data_type->checksum());

    $data_type = $typed_data_manager->create($definition, [
      'slug' => 'bar',
      'title' => 'foo',
    ]);
    $this->assertNotEquals($checksum_first, $data_type->checksum());
  }

}
