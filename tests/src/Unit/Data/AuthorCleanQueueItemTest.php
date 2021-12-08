<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Data;

use Drupal\druki_author\Data\AuthorCleanQueueItem;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for author clean queue item.
 *
 * @coversDefaultClass \Drupal\druki_author\Data\AuthorCleanQueueItem
 */
final class AuthorCleanQueueItemTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $queue_item = new AuthorCleanQueueItem();
    $this->assertNull($queue_item->getPayload());
  }

}
