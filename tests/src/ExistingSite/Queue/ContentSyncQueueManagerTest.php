<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Druki\Tests\Traits\EntityCleanupTrait;
use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Queue\QueueWorkerInterface;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\RequeueException;
use Drupal\Core\Queue\SuspendQueueException;
use Drupal\druki_content\Queue\ContentSyncQueueManager;
use Prophecy\PhpUnit\ProphecyTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content synchronization queue manager.
 *
 * @coversDefaultClass \Drupal\druki_content\Queue\ContentSyncQueueManager
 */
final class ContentSyncQueueManagerTest extends ExistingSiteBase {

  use ProphecyTrait;
  use EntityCleanupTrait;
  use SourceContentProviderTrait;

  /**
   * The content sync queue manager.
   */
  protected ContentSyncQueueManager $queueManager;

  /**
   * The sync queue.
   */
  protected QueueInterface $syncQueue;

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Tests that clear is actually clear queue.
   */
  public function testClear(): void {
    $this->syncQueue->deleteQueue();
    $this->assertEquals(0, $this->syncQueue->numberOfItems());
    $this->syncQueue->createItem('test');
    $this->assertEquals(1, $this->syncQueue->numberOfItems());
    $this->queueManager->delete();
    $this->assertEquals(0, $this->syncQueue->numberOfItems());
  }

  /**
   * Tests that queue is built from path.
   */
  public function testBuildFromPath(): void {
    $directory = $this->setupFakeSourceDir();
    $this->queueManager->buildFromPath($directory->url());
    $this->assertEquals(2, $this->syncQueue->numberOfItems());
  }

  /**
   * Tests that queue can be run by manager.
   */
  public function testRun(): void {
    $this->syncQueue->deleteQueue();
    $this->syncQueue->createItem('valid');
    $this->syncQueue->createItem('requeue');
    $this->syncQueue->createItem('suspend');
    $this->syncQueue->createItem('exception');
    $this->syncQueue->createItem('valid');

    $this->assertEquals(5, $this->syncQueue->numberOfItems());
    $result = $this->queueManager->run();
    $this->assertEquals(0, $this->syncQueue->numberOfItems());
    $this->assertEquals(5, $result);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $queue_worker = $this->prophesize(QueueWorkerInterface::class);
    $queue_worker->processItem('valid')->willReturn(TRUE);
    $queue_worker->processItem('requeue')->will(function ($args) use ($queue_worker) {
      $queue_worker->processItem('requeue')->willReturn(TRUE);
      throw new RequeueException();
    });
    $queue_worker->processItem('suspend')->will(function ($args) use ($queue_worker) {
      $queue_worker->processItem('suspend')->willReturn(TRUE);
      throw new SuspendQueueException();
    });
    $queue_worker->processItem('exception')->will(function ($args) use ($queue_worker) {
      $queue_worker->processItem('exception')->willReturn(TRUE);
      throw new \Exception();
    });

    $queue_worker_manager = $this->prophesize(QueueWorkerManagerInterface::class);
    $queue_worker_manager->createInstance('druki_content_sync')
      ->willReturn($queue_worker->reveal());
    $this->container->set('plugin.manager.queue_worker', $queue_worker_manager->reveal());

    $this->syncQueue = $this->container->get('queue')->get(ContentSyncQueueManager::QUEUE_NAME);
    $this->queueManager = $this->container->get('druki_content.queue.content_sync_manager');
    $this->storeEntityIds(['druki_content', 'media', 'file']);
  }

}
