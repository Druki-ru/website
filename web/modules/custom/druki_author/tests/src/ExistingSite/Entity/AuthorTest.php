<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\ExistingSite\Entity;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlEntityStorageInterface;
use Drupal\druki_author\Entity\Author;
use Drupal\Tests\druki\Traits\EntityCleanupTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for author entity.
 *
 * @coversDefaultClass \Drupal\druki_author\Entity\Author
 */
final class AuthorTest extends ExistingSiteBase {

  use EntityCleanupTrait;

  /**
   * The author entity type.
   */
  protected EntityTypeInterface $authorEntityType;

  /**
   * The author entity storage.
   */
  protected SqlEntityStorageInterface $authorStorage;

  /**
   * The media entity storage.
   */
  protected SqlEntityStorageInterface $mediaStorage;

  /**
   * Tests that all base fields are set and returned properly.
   *
   * @dataProvider baseFieldProvider
   */
  public function testBaseFieldDefinitions(string $name, string $type): void {
    $base_fields = Author::baseFieldDefinitions($this->authorEntityType);
    $this->assertArrayHasKey($name, $base_fields);
    $this->assertEquals($type, $base_fields[$name]->getType());
  }

  /**
   * Provides expected base fields and their types.
   */
  public function baseFieldProvider(): array {
    return [
      'id' => ['id', 'string'],
      'name_given' => ['name_given', 'string'],
      'name_family' => ['name_family', 'string'],
      'country' => ['country', 'string'],
      'org_name' => ['org_name', 'string'],
      'org_unit' => ['org_unit', 'string'],
      'homepage' => ['homepage', 'uri'],
      'description' => ['description', 'map'],
      'image' => ['image', 'entity_reference'],
      'checksum' => ['checksum', 'string'],
      'identification' => ['identification', 'druki_identification'],
    ];
  }

  /**
   * Tests that creating entity without explicit set ID is throws exception.
   */
  public function testEntitySaveWithoutId(): void {
    $author = $this->authorStorage->create();
    $this->expectException(EntityStorageException::class);
    $author->save();
  }

  /**
   * Tests that entity provides expected cache tags.
   */
  public function testCacheTags(): void {
    /** @var \Drupal\druki_author\Entity\AuthorInterface $author */
    $author = $this->authorStorage->create();
    $author->setId('test');
    $author->enforceIsNew(FALSE);

    $cache_tags = [
      'druki_author:test',
    ];
    $this->assertEquals($cache_tags, $author->getCacheTags());

    $author->addIdentification('email', 'john.doe@example.com');
    $cache_tags[] = 'druki_author:identification:email:john.doe@example.com';
    $this->assertSame($cache_tags, $author->getCacheTags());
  }

  /**
   * Tests that entity has proper access.
   */
  public function testAccess(): void {
    $author = $this->authorStorage->create();
    $this->assertFalse($author->access('edit'));
    $this->assertTrue($author->access('view'));
  }

  /**
   * Tests custom methods for entity.
   */
  public function testEntityMethods(): void {
    /** @var \Drupal\druki_author\Entity\AuthorInterface $author */
    $author = $this->authorStorage->create();
    $author->setId('test');
    $this->assertEquals('test', $author->id());

    $author->setName('Foo', 'Bar');
    $this->assertEquals('Foo', $author->getNameGiven());
    $this->assertEquals('Bar', $author->getNameFamily());

    $this->assertIsString($author->label());

    $author->setCountry('RU');
    $this->assertEquals('RU', $author->getCountry());

    $this->assertFalse($author->hasOrganization());
    $this->assertNull($author->getOrganizationName());
    $this->assertNull($author->getOrganizationUnit());
    $author->setOrganization('Self-employee', 'Drupal Developer');
    $this->assertTrue($author->hasOrganization());
    $this->assertEquals('Self-employee', $author->getOrganizationName());
    $this->assertEquals('Drupal Developer', $author->getOrganizationUnit());
    $author->clearOrganization();
    $this->assertFalse($author->hasOrganization());

    $this->assertFalse($author->hasHomepage());
    $this->assertNull($author->getHomepage());
    $author->setHomepage('https://example.com');
    $this->assertTrue($author->hasHomepage());
    $this->assertEquals('https://example.com', $author->getHomepage());
    $author->clearHomepage();
    $this->assertFalse($author->hasHomepage());

    $this->assertFalse($author->hasDescription());
    $this->assertNull($author->getDescription());
    $author->setDescription(['en' => 'Hello, World!']);
    $this->assertTrue($author->hasDescription());
    $this->assertEquals(['en' => 'Hello, World!'], $author->getDescription());
    $author->clearDescription();
    $this->assertFalse($author->hasDescription());

    $media = $this->mediaStorage->create(['bundle' => 'image']);
    $media->save();
    $this->assertFalse($author->hasImage());
    $this->assertNull($author->getImageId());
    $this->assertNull($author->getImageMedia());
    $author->setImageMedia($media);
    $this->assertTrue($author->hasImage());
    $this->assertEquals($media->id(), $author->getImageId());
    $this->assertEquals($media->id(), $author->getImageMedia()->id());
    $author->clearImage();
    $this->assertFalse($author->hasImage());

    $this->assertTrue($author->get('identification')->isEmpty());
    $author->addIdentification('email', 'john.doe@example.com');
    $this->assertFalse($author->get('identification')->isEmpty());
    $author->setIdentification([
      ['type' => 'email', 'value' => 'jane.doe@example.com'],
      ['invalid'],
    ]);
    $this->assertEquals(['type' => 'email', 'value' => 'jane.doe@example.com'], $author->get('identification')->first()->getValue());
    $author->clearIdentification();
    $this->assertTrue($author->get('identification')->isEmpty());

    $author->setChecksum('foo-bar');
    $this->assertEquals('foo-bar', $author->getChecksum());
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->authorEntityType = $this->container->get('entity_type.manager')
      ->getDefinition('druki_author');
    $this->authorStorage = $this->container->get('entity_type.manager')
      ->getStorage('druki_author');
    $this->mediaStorage = $this->container->get('entity_type.manager')
      ->getStorage('media');
    $this->storeEntityIds(['druki_author', 'media', 'file']);
  }

}
