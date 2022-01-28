<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_redirect\Unit\Data;

use Drupal\druki_redirect\Data\RedirectCleanQueueItem;
use Drupal\Tests\UnitTestCase;

/**
 * Provides redirect clean queue item.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\RedirectCleanQueueItem
 */
final class RedirectCleanQueueItemTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $queue_item = new RedirectCleanQueueItem();
    $this->assertNull($queue_item->getPayload());
  }

}
