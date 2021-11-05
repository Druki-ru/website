<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Drupal\druki_content\Data\ContentSourceFileList;
use Drupal\druki_content\Data\ContentSourceFileListQueueItem;
use Drupal\Tests\UnitTestCase;

/**
 * Provides content source file list queue item.
 *
 * @coversDefaultClass \Drupal\druki_content\Data\ContentSourceFileListQueueItem
 */
final class ContentSourceFileListQueueItemTest extends UnitTestCase {

  /**
   * Test that object works as expected.
   */
  public function testObject(): void {
    $content_source_file_list = new ContentSourceFileList();
    $queue_item = new ContentSourceFileListQueueItem($content_source_file_list);
    $this->assertEquals($content_source_file_list, $queue_item->getPayload());
  }

}
