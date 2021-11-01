<?php

declare(strict_types=1);

namespace Druki\Tests\Unit;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_redirect\Data\RedirectFile;
use Drupal\druki_redirect\Data\RedirectFileList;
use Drupal\druki_redirect\Data\RedirectFileListQueueItem;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for redirect file list queue item.
 *
 * @coversDefaultClass \Drupal\druki_redirect\Data\RedirectFileListQueueItem
 */
final class RedirectFileListQueueItemTest extends UnitTestCase {

  use SourceContentProviderTrait;

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $root = $this->setupFakeSourceDir();
    $file_pathname = $root->url() . '/docs/ru/redirects.csv';

    $list = new RedirectFileList();
    $redirect_file = new RedirectFile($file_pathname, 'ru');
    $list->addFile($redirect_file);

    $queue_item = new RedirectFileListQueueItem($list);
    $this->assertEquals($list, $queue_item->getPayload());
  }

}
