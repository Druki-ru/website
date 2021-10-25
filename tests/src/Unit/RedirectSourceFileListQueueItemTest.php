<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\RedirectSourceFileList;
use Drupal\druki_content\Data\RedirectSourceFileListQueueItem;
use Drupal\Tests\UnitTestCase;

/**
 * Provides content source file list queue item.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\RedirectSourceFileListQueueItem
 */
final class RedirectSourceFileListQueueItemTest extends UnitTestCase {

  /**
   * Test that object works as expected.
   */
  public function testObject(): void {
    $redirect_source_file_list = new RedirectSourceFileList();
    $queue_item = new RedirectSourceFileListQueueItem($redirect_source_file_list);
    $this->assertEquals($redirect_source_file_list, $queue_item->getPayload());
  }

}
