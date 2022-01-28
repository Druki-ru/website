<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_redirect\Unit\Data;

use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\Tests\UnitTestCase;

/**
 * Provides content source file list queue item.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\RedirectFileListQueueItem
 */
final class RedirectSourceFileListQueueItemTest extends UnitTestCase {

  /**
   * Test that object works as expected.
   */
  public function testObject(): void {
    $redirect_source_file_list = new RedirectFileList();
    $queue_item = new RedirectFileListQueueItem($redirect_source_file_list);
    $this->assertEquals($redirect_source_file_list, $queue_item->getPayload());
  }

}
