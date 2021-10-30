<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Queue;

use Drupal\druki_content\Queue\ContentSyncQueueItemInterface;
use Drupal\druki_content\Queue\ContentSyncQueueProcessorInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for main content sync queue processor (manager).
 *
 * @coversDefaultClass \Drupal\druki_content\Queue\ChainContentSyncQueueProcessor
 */
final class ChainContentSyncQueueProcessor extends ExistingSiteBase {

  use ProphecyTrait;

  /**
   * Checks that processor calls other processors properly.
   */
  public function testProcessor(): void {
    /** @var \Drupal\druki_content\Queue\ChainContentSyncQueueProcessor $processor */
    $processor = $this->container->get('druki_content.queue.content_sync_processor');
    $queue_item = new class() implements ContentSyncQueueItemInterface {

      public function getPayload(): mixed {
        return 'string';
      }

    };

    $item_processor = new class() implements ContentSyncQueueProcessorInterface {

      protected mixed $value = NULL;

      public function process(ContentSyncQueueItemInterface $item): array {
        $this->value = $item->getPayload();
        return [];
      }

      public function getValue(): mixed {
        return $this->value;
      }

      public function isApplicable(ContentSyncQueueItemInterface $item): bool {
        return $item->getPayload() == 'string';
      }

    };

    $this->assertTrue($processor->isApplicable($queue_item));
    $this->assertNull($item_processor->getValue());
    $processor->addProcessor($item_processor);
    $processor->process($queue_item);
    $this->assertEquals($queue_item->getPayload(), $item_processor->getValue());
  }

}
