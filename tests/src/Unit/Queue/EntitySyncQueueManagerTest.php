<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Queue;

use Drupal\Core\Queue\QueueInterface;
use Drupal\druki\Data\EntitySyncQueueItemList;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueManager;
use Drupal\druki\Repository\EntitySyncQueueStateInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for entity sync queue manager.
 *
 * @coversDefaultClass \Drupal\druki\Queue\EntitySyncQueueManager
 */
final class EntitySyncQueueManagerTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that queue manager works as expected.
   */
  public function testManager(): void {
    $queue = $this->buildQueue();
    $queue_state = $this->buildQueueState();
    $queue_manager = new EntitySyncQueueManager($queue, $queue_state);
    $this->assertSame($queue, $queue_manager->getQueue());
    $this->assertSame($queue_state, $queue_manager->getState());
    $queue_manager->delete();

    $queue_item = new class() implements EntitySyncQueueItemInterface {
      public function getPayload(): mixed {
        return NULL;
      }
    };
    $queue_items = new EntitySyncQueueItemList();
    $queue_items->addQueueItem($queue_item);
    $queue_items->addQueueItem($queue_item);
    $queue_items->addQueueItem($queue_item);
    $queue_manager->fillQueue($queue_items);
  }

  /**
   * Builds mock of queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   The mock of queue.
   */
  protected function buildQueue(): QueueInterface {
    $queue = $this->prophesize(QueueInterface::class);
    $queue->deleteQueue()->shouldBeCalled();
    $queue->createItem(Argument::type(EntitySyncQueueItemInterface::class))
      ->shouldBeCalledTimes(3);
    return $queue->reveal();
  }

  /**
   * Builds mock of queue state.
   *
   * @return \Drupal\druki\Repository\EntitySyncQueueStateInterface
   *   The mock of queue state.
   */
  protected function buildQueueState(): EntitySyncQueueStateInterface {
    $queue_state = $this->prophesize(EntitySyncQueueStateInterface::class);
    $queue_state->delete()->shouldBeCalled();
    return $queue_state->reveal();
  }

}
