<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_redirect\Unit\Data;

use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides test for redirect file list queue item.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\RedirectFileListQueueItem
 */
final class RedirectFileListQueueItemTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    vfsStream::setup();

    vfsStream::create([
      'redirects.csv' => '',
    ]);

    $list = new RedirectFileList();
    $redirect_file = new RedirectFile(vfsStream::url('root/redirects.csv'), 'ru');
    $list->addFile($redirect_file);

    $queue_item = new RedirectFileListQueueItem($list);
    $this->assertEquals($list, $queue_item->getPayload());
  }

}
