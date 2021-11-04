<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_redirect\Queue\RedirectSyncQueueManager;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for redirect sync queue manager.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Queue\RedirectSyncQueueManager
 */
final class RedirectSyncQueueManagerTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * The redirect sync queue manager.
   */
  protected RedirectSyncQueueManager $queueManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->queueManager = $this->container->get('druki_redirect.queue.sync_manager');
    $this->queueManager->delete();
  }

  /**
   * Tests that delete method works as expected.
   */
  public function testDelete(): void {
    $queue = $this->queueManager->getQueue();
    $state = $this->queueManager->getState();

    $this->assertEquals(0, $queue->numberOfItems());
    $this->assertEquals([], $state->getStoredEntityIds());

    $queue->createItem('test');
    $state->storeEntityIds([123]);

    $this->assertEquals(1, $queue->numberOfItems());
    $this->assertEquals([123], $state->getStoredEntityIds());

    $this->queueManager->delete();
    $this->assertEquals(0, $queue->numberOfItems());
    $this->assertEquals([], $state->getStoredEntityIds());
  }

  /**
   * Tests that building queue from directories works as expected.
   */
  public function testBuildFromDirectories(): void {
    $directory = $this->setupFakeSourceDir();
    $this->queueManager->buildFromDirectories([$directory->url() . '/docs']);
    // 1 item for redirect file list and second for clean up.
    $this->assertEquals(2, $this->queueManager->getQueue()->numberOfItems());
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->queueManager->delete();
    parent::tearDown();
  }

}
