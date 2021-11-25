<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Druki\Tests\Traits\EntityCleanupTrait;
use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki_author\Data\Author;
use Drupal\druki_author\Data\AuthorList;
use Drupal\druki_author\Data\AuthorListQueueItem;
use Drupal\druki_author\Queue\AuthorListQueueItemProcessor;
use Drupal\media\MediaInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for author list queue item processor.
 *
 * @coversDefaultClass \Drupal\druki_author\Queue\AuthorListQueueItemProcessor
 */
final class AuthorListQueueItemProcessorTest extends ExistingSiteBase {

  use EntityCleanupTrait;
  use SourceContentProviderTrait;

  /**
   * The author storage.
   */
  protected ContentEntityStorageInterface $authorStorage;

  /**
   * The author list queue item processor.
   */
  protected AuthorListQueueItemProcessor $processor;

  /**
   * Tests that only expected queue item is allowed.
   */
  public function testIsApplicable(): void {
    $invalid = new class() implements EntitySyncQueueItemInterface {

      public function getPayload(): mixed {
        return NULL;
      }

    };
    $this->assertFalse($this->processor->isApplicable($invalid));

    $valid = new AuthorListQueueItem(new AuthorList());
    $this->assertTrue($this->processor->isApplicable($valid));
  }

  /**
   * Tests that processing existing author works as expected.
   */
  public function testProcessExistingAuthor(): void {
    $author = Author::createFromArray('test', [
      'name' => [
        'given' => 'John',
        'family' => 'Doe',
      ],
      'country' => 'RU',
    ]);

    $author_entity = $this->authorStorage->create();
    $author_entity->setId('test');
    $author_entity->setChecksum($author->checksum());
    $author_entity->save();

    $author_list = new AuthorList();
    $author_list->addAuthor($author);
    $queue_item = new AuthorListQueueItem($author_list);

    $count_before = $this->authorStorage->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();

    $result = $this->processor->process($queue_item);
    $this->assertSame([$author_entity->id()], $result);

    $count_after = $this->authorStorage->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
    $this->assertEquals($count_before, $count_after);
  }

  /**
   * Tests that processing not existing author works as expected.
   */
  public function testProcessNotExistingAuthor(): void {
    $directory = $this->setupFakeSourceDir();

    $author = Author::createFromArray('test', [
      'name' => [
        'given' => 'John',
        'family' => 'Doe',
      ],
      'org' => [
        'name' => 'Foo Bar',
        'unit' => 'Development',
      ],
      'homepage' => 'https://example.com',
      'description' => [
        'en' => 'Foo bar desc.',
      ],
      'image' => $directory->url() . '/authors/image/dries.jpg',
      'country' => 'RU',
      'identification' => [
        'email' => [
          'john.doe@example.com',
        ],
      ],
    ]);


    $author_list = new AuthorList();
    $author_list->addAuthor($author);
    $queue_item = new AuthorListQueueItem($author_list);

    $count_before = $this->authorStorage->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();

    $result = $this->processor->process($queue_item);
    $this->assertContains($author->getId(), $result);

    $count_after = $this->authorStorage->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
    $this->assertEquals($count_after, $count_before + 1);

    /** @var \Drupal\druki_author\Entity\AuthorInterface $author_entity */
    $author_entity = $this->authorStorage->load($author->getId());
    $this->assertEquals($author->checksum(), $author_entity->getChecksum());
    $this->assertEquals($author->getNameGiven(), $author_entity->getNameGiven());
    $this->assertEquals($author->getNameFamily(), $author_entity->getNameFamily());
    $this->assertEquals($author->getOrgName(), $author_entity->getOrganizationName());
    $this->assertEquals($author->getOrgUnit(), $author_entity->getOrganizationUnit());
    $this->assertEquals($author->getHomepage(), $author_entity->getHomepage());
    $this->assertEquals($author->getDescription(), $author_entity->getDescription());
    $this->assertInstanceOf(MediaInterface::class, $author_entity->getImageMedia());
    $this->assertEquals(
      [['type' => 'email', 'value' => 'john.doe@example.com']],
      $author_entity->get('identification')->getValue(),
    );
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
    $this->storeEntityIds(['druki_author', 'media', 'file']);
    $this->authorStorage = $this->container->get('entity_type.manager')
      ->getStorage('druki_author');
    $this->processor = $this->container
      ->get('druki_author.queue.author_list_queue_item_processor');
  }

}
