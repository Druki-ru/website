<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Unit\Queue;

use Drupal\druki\Queue\ChainEntitySyncQueueItemProcessor;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for chained entity sync queue item processor.
 *
 * @coversDefaultClass \Drupal\druki\Queue\ChainEntitySyncQueueItemProcessor
 */
final class ChainEntitySyncQueueItemProcessorTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that processor works as expected.
   */
  public function testProcessor(): void {
    $processor = new ChainEntitySyncQueueItemProcessor();

    $queue_item_1 = new class() implements EntitySyncQueueItemInterface {

      // @phpcs:ignore Drupal.Commenting.FunctionComment.Missing
      public function getPayload(): mixed {
        return 'string';
      }

    };

    $queue_item_2 = new class() implements EntitySyncQueueItemInterface {

      // @phpcs:ignore Drupal.Commenting.FunctionComment.Missing
      public function getPayload(): mixed {
        return NULL;
      }

    };

    // @codingStandardsIgnoreStart
    $item_processor = new class() implements EntitySyncQueueItemProcessorInterface {

      protected mixed $value = NULL;

      public function process(EntitySyncQueueItemInterface $item): array {
        $this->value = $item->getPayload();
        return [1, 2, 3];
      }

      public function getValue(): mixed {
        return $this->value;
      }

      public function isApplicable(EntitySyncQueueItemInterface $item): bool {
        return $item->getPayload() == 'string';
      }

    };
    // @codingStandardsIgnoreEnd

    $processor->addProcessor($item_processor);

    // The processor should return TRUE every time, this method doesn't used
    // directly so it's doesnt matter.
    $this->assertTrue($processor->isApplicable($queue_item_1));
    $this->assertTrue($processor->isApplicable($queue_item_2));

    $this->assertTrue($item_processor->isApplicable($queue_item_1));
    $this->assertNull($item_processor->getValue());
    $ids = $processor->process($queue_item_1);
    $this->assertEquals($queue_item_1->getPayload(), $item_processor->getValue());
    $this->assertEquals([1, 2, 3], $ids);

    $this->assertFalse($item_processor->isApplicable($queue_item_2));
    $ids = $processor->process($queue_item_2);
    $this->assertEmpty($ids);
  }

}
