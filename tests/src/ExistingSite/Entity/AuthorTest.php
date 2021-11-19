<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Entity;

use Druki\Tests\Traits\EntityCleanupTrait;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlEntityStorageInterface;
use Drupal\druki_author\Entity\Author;
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
