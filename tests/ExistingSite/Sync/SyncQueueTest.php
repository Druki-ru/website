<?php

namespace Druki\Tests\ExistingSite\Sync;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Druki\Tests\Traits\EntityCleanupTrait;
use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\Core\Queue\QueueInterface;
use Drupal\druki_content\Sync\Clean\CleanQueueItem;
use Drupal\druki_content\Sync\Redirect\RedirectFile;
use Drupal\druki_content\Sync\Redirect\RedirectFileList;
use Drupal\druki_content\Sync\Redirect\RedirectQueueItem;
use Drupal\druki_content\Sync\SourceContent\SourceContent;
use Drupal\druki_content\Sync\SourceContent\SourceContentList;
use Drupal\druki_content\Sync\SourceContent\SourceContentListQueueItem;
use org\bovigo\vfs\vfsStream;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Tests queue used for synchronizations.
 */
final class SyncQueueTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;
  use SourceContentProviderTrait;
  use EntityCleanupTrait;

  /**
   * The source content fake root.
   *
   * @var \org\bovigo\vfs\vfsStreamDirectory
   */
  protected $sourceRoot;

  /**
   * The sync queue manager.
   *
   * @var \Drupal\druki_content\Sync\Queue\QueueManager
   */
  protected $syncQueueManager;

  /**
   * The queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * Tests queue is building from the path.
   */
  public function testBuildQueueFromPath(): void {
    $this->syncQueueManager->buildFromPath($this->sourceRoot->url());
    // 2 content files, 1 redirect file.
    $this->assertEquals(3, $this->getSyncQueue()->numberOfItems());
  }

  /**
   * Gets queue used for synchronization.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   The queue object.
   */
  protected function getSyncQueue(): QueueInterface {
    return $this->queueFactory->get($this->syncQueueManager::QUEUE_NAME);
  }

  /**
   * Tests that queue is appropriately cleared.
   */
  public function testQueueClear(): void {
    $this->syncQueueManager->buildFromPath($this->sourceRoot->url());
    $this->assertEquals(3, $this->getSyncQueue()->numberOfItems());
    $this->syncQueueManager->clear();
    $this->assertEquals(0, $this->getSyncQueue()->numberOfItems());
  }

  /**
   * Tests that queue executed.
   */
  public function testSyncQueue(): void {
    $file = vfsStream::newFile('example-content.md')
      ->withContent(\file_get_contents(__DIR__ . '/../../fixtures/source-content.md'))
      ->at($this->sourceRoot);
    $source_content = new SourceContent($file->url(), $file->path(), 'ru');
    $source_content_list = new SourceContentList();
    $source_content_list->add($source_content);

    $queue = $this->getSyncQueue();
    $this->assertEquals(0, $queue->numberOfItems());
    $queue->createItem(new SourceContentListQueueItem($source_content_list));
    $this->assertEquals(1, $queue->numberOfItems());

    /** @var \Drupal\druki_content\Entity\Handler\Storage\DrukiContentStorage $druki_content_storage */
    $druki_content_storage = $this->container->get('entity_type.manager')->getStorage('druki_content');
    $druki_content = $druki_content_storage->loadBySlug('example');
    $this->assertNull($druki_content);

    $this->syncQueueManager->run();
    $this->assertEquals(0, $queue->numberOfItems());
    $druki_content = $druki_content_storage->loadBySlug('example');
    $this->assertEquals('example', $druki_content->getSlug());
    $this->assertEquals('The title', $druki_content->label());

    // Emulate that file is updated while content present on the site.
    $file = vfsStream::newFile('example-content.md')
      ->withContent(\file_get_contents(__DIR__ . '/../../fixtures/source-content-2.md'))
      ->at($this->sourceRoot);
    $source_content = new SourceContent($file->url(), $file->path(), 'ru');
    $source_content_list = new SourceContentList();
    $source_content_list->add($source_content);
    $queue->createItem(new SourceContentListQueueItem($source_content_list));

    $old_id = $druki_content->id();
    $this->syncQueueManager->run();
    $druki_content = $druki_content_storage->loadBySlug('example');
    // It should update an existed content, not delete and create new one.
    $this->assertEquals($old_id, $druki_content->id());
    $this->assertEquals('The modified version of source content', $druki_content->label());
  }

  /**
   * Tests syncing redirects.
   */
  public function testRedirectQueue(): void {
    $file_pathname = $this->sourceRoot->url() . '/docs/ru/redirects.csv';
    $redirect_file_list = new RedirectFileList();
    $redirect_file_list->addFile(new RedirectFile($file_pathname, 'ru'));
    $redirect_queue_item = new RedirectQueueItem($redirect_file_list);

    $queue = $this->getSyncQueue();
    $queue->createItem($redirect_queue_item);
    $this->syncQueueManager->run();

    $this->drupalGet('/foo-bar');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->addressEquals('/');

    $this->drupalGet('/foo-bar', ['query' => ['with' => 'query']]);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->addressEquals('/?with=query');

    $this->drupalGet('/foo-baz');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->addressEquals('/#fragment');
  }

  /**
   * Tests clean operations.
   */
  public function testCleanQueue(): void {
    $this->createDrukiContent([
      'slug' => 'test_clean_up',
      'sync_timestamp' => 1,
    ]);

    /** @var \Drupal\druki_content\Entity\Handler\Storage\DrukiContentStorage $druki_content_storage */
    $druki_content_storage = $this->container->get('entity_type.manager')->getStorage('druki_content');
    $druki_content = $druki_content_storage->loadBySlug('test_clean_up');
    $this->assertEquals('test_clean_up', $druki_content->getSlug());

    $queue_item = new CleanQueueItem(2);
    $queue = $this->getSyncQueue();
    $queue->createItem($queue_item);
    $this->syncQueueManager->run();

    $druki_content = $druki_content_storage->loadBySlug('test_clean_up');
    $this->assertNull($druki_content);
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    // Clean state for clean queue, so it will run it next time.
    $this->container->get('state')->delete('druki_content:redirect_last_hash:ru');
    parent::tearDown();
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->sourceRoot = $this->setupFakeSourceDir();
    $this->queueFactory = $this->container->get('queue');
    $this->syncQueueManager = $this->container->get('druki_content.sync_queue_manager');

    // Make sure the queue is empty during testing.
    $this->queueFactory->get($this->syncQueueManager::QUEUE_NAME)->deleteQueue();
    $this->storeEntityIds(['druki_content', 'media', 'file', 'paragraph', 'redirect']);
  }

}
