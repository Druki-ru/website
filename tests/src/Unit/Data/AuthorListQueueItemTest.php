<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Data;

use Drupal\druki_author\Data\AuthorList;
use Drupal\druki_author\Data\AuthorListQueueItem;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for author list queue item.
 *
 * @coversDefaultClass \Drupal\druki_author\Data\AuthorListQueueItem
 */
final class AuthorListQueueItemTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $author_list = new AuthorList();
    $queue_item = new AuthorListQueueItem($author_list);
    $this->assertSame($author_list, $queue_item->getPayload());
  }

}
