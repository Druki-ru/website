<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Druki\Tests\Traits\EntityCleanupTrait;
use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\druki_content\Data\ContentSourceFileList;
use Drupal\druki_content\Data\ContentSourceFileListQueueItem;
use Drupal\druki_content\Entity\DrukiContentInterface;
use org\bovigo\vfs\vfsStream;
use Prophecy\PhpUnit\ProphecyTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content source file list queue item processor.
 *
 * @coversDefaultClass \Drupal\druki_content\Queue\ContentSourceFileListQueueItemProcessor
 */
final class ContentSourceFileListQueueItemProcessorTest extends ExistingSiteBase {

  use EntityCleanupTrait;
  use ProphecyTrait;
  use SourceContentProviderTrait;

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Tests that processor works as expected.
   */
  public function testProcessor(): void {
    $files_root = $this->setupFakeSourceDir();
    /** @var \Drupal\druki_content\Queue\ContentSourceFileListQueueItemProcessor $processor */
    $processor = $this->container->get('druki_content.queue.content_source_file_list_queue_item');
    /** @var \Drupal\druki_content\Repository\DrukiContentStorage $content_storage */
    $content_storage = $this->container->get('entity_type.manager')->getStorage('druki_content');

    $content_source_file_list = new ContentSourceFileList();
    $content_source_file = new ContentSourceFile($files_root->url() . '/docs/ru/drupal/index.md', 'index.md', 'ru');
    $content_source_file_list->addFile($content_source_file);
    $queue_item = new ContentSourceFileListQueueItem($content_source_file_list);

    $this->assertNull($content_storage->loadBySlug('test/example'));

    $processor->process($queue_item);

    $content_entity = $content_storage->loadBySlug('test/example');
    $this->assertInstanceOf(DrukiContentInterface::class, $content_entity);
    $this->assertEquals('The title', $content_entity->getTitle());
    $this->assertEquals('index.md', $content_entity->getRelativePathname());
    $content_document = $content_entity->getContentDocument();
    $this->assertInstanceOf(ContentDocument::class, $content_document);
    $this->assertNull($content_entity->getCore());
    $this->assertTrue($content_entity->get('search_keywords')->isEmpty());
    $this->assertFalse($content_entity->get('metatags')->isEmpty());
    $this->assertNull($content_entity->getCategory());

    // Process it second time.
    $processor->process($queue_item);
    $content_entity_2 = $content_storage->loadBySlug('test/example');
    $this->assertEquals($content_entity->id(), $content_entity_2->id());

    $files_root = $this->setupFakeSourceDirUpdate();
    $content_source_file_list = new ContentSourceFileList();
    $content_source_file = new ContentSourceFile($files_root->url() . '/docs/ru/drupal/index.md', 'index.md', 'ru');
    $content_source_file_list->addFile($content_source_file);
    $queue_item = new ContentSourceFileListQueueItem($content_source_file_list);

    $processor->process($queue_item);
    $content_entity_3 = $content_storage->loadBySlug('test/example');
    // Make sure that we update the same entity.
    $this->assertEquals($content_entity->id(), $content_entity_3->id());
    $this->assertEquals(9, $content_entity_3->getCore());
    $this->assertFalse($content_entity_3->get('search_keywords')->isEmpty());
    $this->assertIsArray($content_entity_3->getCategory());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storeEntityIds(['druki_content', 'media', 'file']);
  }

}
