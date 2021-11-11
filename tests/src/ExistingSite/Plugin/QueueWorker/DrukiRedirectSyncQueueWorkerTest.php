<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerManager;
use Drupal\druki\Queue\ChainEntitySyncQueueItemProcessorInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for 'druki_redirect_sync' queue worker.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Plugin\QueueWorker\DrukiRedirectSyncQueueWorker
 */
final class DrukiRedirectSyncQueueWorkerTest extends ExistingSiteBase {

  use ProphecyTrait;

  /**
   * The queue processor.
   */
  protected ChainEntitySyncQueueItemProcessorInterface $chainQueueProcessor;

  /**
   * The queue worker plugin manager.
   */
  protected QueueWorkerManager $queueWorkerManager;

  /**
   * Test that worker call processor as expected.
   */
  public function testWorker(): void {
    $plugin = $this->queueWorkerManager->createInstance('druki_redirect_sync');
    $this->assertFalse($this->chainQueueProcessor->isCalled());
    $plugin->processItem('random data');
    $this->assertFalse($this->chainQueueProcessor->isCalled());
    $valid_queue_item = $this->prophesize(EntitySyncQueueItemInterface::class);
    $plugin->processItem($valid_queue_item->reveal());
    $this->assertTrue($this->chainQueueProcessor->isCalled());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $chain_queue_processor = new class() implements ChainEntitySyncQueueItemProcessorInterface {

      protected bool $called = FALSE;

      public function addProcessor(EntitySyncQueueItemProcessorInterface $processor): void {

      }

      public function isApplicable(EntitySyncQueueItemInterface $item): bool {
        return TRUE;
      }

      public function process(EntitySyncQueueItemInterface $item): array {
        $this->called = TRUE;
        return [];
      }

      public function isCalled(): bool {
        return $this->called;
      }

    };

    $this->container->set('druki.queue.chain_entity_sync_processor', $chain_queue_processor);
    $this->chainQueueProcessor = $this->container->get('druki.queue.chain_entity_sync_processor');
    $this->queueWorkerManager = $this->container->get('plugin.manager.queue_worker');
  }

}
