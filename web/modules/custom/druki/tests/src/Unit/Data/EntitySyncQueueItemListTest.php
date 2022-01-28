<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Unit\Data;

use Drupal\druki\Data\EntitySyncQueueItemList;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for entity sync queue item collection.
 *
 * @coversDefaultClass \Drupal\druki\Data\EntitySyncQueueItemList
 */
final class EntitySyncQueueItemListTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $queue_item_1 = $this->prophesize(EntitySyncQueueItemInterface::class)->reveal();
    $queue_item_2 = $this->prophesize(EntitySyncQueueItemInterface::class)->reveal();

    $list = new EntitySyncQueueItemList();
    $this->assertEquals(0, $list->getIterator()->count());
    $list->addQueueItem($queue_item_1);
    $list->addQueueItem($queue_item_2);
    $this->assertEquals(2, $list->getIterator()->count());
    $this->assertEquals($queue_item_1, $list->getIterator()->current());
  }

}
